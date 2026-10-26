<?php

namespace App\Libraries;

/** Small dependency-free PDF writer for the printable IT Accountability Form. */
class AccountabilityPdf
{
    private const PAGE_WIDTH = 612;
    private const PAGE_HEIGHT = 1008;
    private array $pages = [];
    private string $content = '';
    private array $settings = [];
    private array $currentRequest = [];
    private array $currentItems = [];
    private array $extraImages = [];
    private ?int $pageBackgroundIndex = null;
    private float $marginLeft = 54;
    private float $marginRight = 54;
    private float $marginTop = 108;
    private float $marginBottom = 72;
    private float $contentWidth = 504;
    private float $headerBottomY = 845;

    public function generate(array $request, array $items, array $settings = []): string
    {
        $this->settings = $settings + ['header_text' => 'KINECT INC.', 'document_title' => 'IT ACCOUNTABILITY FORM', 'equipment_heading' => 'IT Equipment Units', 'footer_text' => 'www.kinectinc.com'];
        $this->currentRequest = $request;
        $this->currentItems = $items;
        $this->pages = [];
        $this->content = '';
        $this->extraImages = [];
        $this->marginLeft = max(18, min(144, (float) ($this->settings['margin_left'] ?? 0.75) * 72));
        $this->marginRight = max(18, min(144, (float) ($this->settings['margin_right'] ?? 0.75) * 72));
        $this->marginTop = max(36, min(180, (float) ($this->settings['margin_top'] ?? 1.5) * 72));
        $this->marginBottom = max(18, min(144, (float) ($this->settings['margin_bottom'] ?? 1.0) * 72));
        $this->contentWidth = self::PAGE_WIDTH - $this->marginLeft - $this->marginRight;
        $this->pageBackgroundIndex = null;
        $this->preparePageBackground();
        $this->startPage($request);
        $this->writeEditableHeader();
        if (!empty($this->settings['body_html'])) {
            $this->renderEditableBody((string) $this->settings['body_html'], $request, $items);
        } else {
            $this->writeHeader($request); $this->writeEmployeeInfo($request); $this->writeEquipmentTable($request, $items); $this->writeAcknowledgment($request);
        }
        $this->finishPage();
        return $this->buildPdf();
    }

    private function startPage(array $request): void
    {
        if ($this->content !== '') $this->finishPage();
        $this->content = '';
        if ($this->pageBackgroundIndex !== null) {
            $image = $this->extraImages[$this->pageBackgroundIndex];
            $scale = max(self::PAGE_WIDTH / $image['width'], self::PAGE_HEIGHT / $image['height']);
            $width = $image['width'] * $scale; $height = $image['height'] * $scale;
            $x = (self::PAGE_WIDTH - $width) / 2; $y = (self::PAGE_HEIGHT - $height) / 2;
            $this->content .= 'q 0 0 ' . self::PAGE_WIDTH . ' ' . self::PAGE_HEIGHT . ' re W n ' . $width . ' 0 0 ' . $height . ' ' . $x . ' ' . $y . ' cm /Im' . $this->pageBackgroundIndex . " Do Q\n";
        }
        if ($this->pages !== []) {
            $this->writeEditableHeader();
        }
    }

    private function writeHeader(array $request): void
    {
        $this->content .= "q 100 0 0 32 48 942 cm /Logo Do Q\n";
        foreach (array_slice($this->richTextLines((string) ($this->settings['header_text'] ?? ''), 55), 0, 3) as $i => $line) $this->renderRichLine($line, 165, 954 - ($i * 12), $i === 0 ? 10 : 8, 399);
        $this->text(48, 900, $this->settings['document_title'], 18, true);
        $this->text(430, 900, (string) ($request['document_number'] ?? ''), 10, true);
        $this->line(48, 888, 564, 888);
    }


    private function writeEditableHeader(): void
    {
        $header = $this->resolveTemplateFields((string) ($this->settings['header_text'] ?? ''), $this->currentRequest, $this->currentItems);
        $top = self::PAGE_HEIGHT - 36;
        $headerBottom = $top;
        if (str_contains($header, '[[KINECT_LOGO]]')) {
            $logoWidth = 170;
            $logoX = $this->marginLeft;
            $logoTop = $top;
            if (preg_match('/<img\b[^>]*src=["\']\[\[KINECT_LOGO\]\]["\'][^>]*style=["\'][^"\']*width\s*:\s*(25|50|75|100)%/i', $header, $size)) $logoWidth = $this->contentWidth * (int) $size[1] / 100;
            elseif (preg_match('/<img\b[^>]*src=["\']\[\[KINECT_LOGO\]\]["\'][^>]*style=["\'][^"\']*width\s*:\s*(\d+(?:\.\d+)?)px/i', $header, $size)) $logoWidth = (float) $size[1] * 0.75;
            if (preg_match('/<img\b[^>]*src=["\']\[\[KINECT_LOGO\]\]["\'][^>]*style=["\'][^"\']*position\s*:\s*absolute[^"\']*left\s*:\s*(-?\d+(?:\.\d+)?)px[^"\']*top\s*:\s*(-?\d+(?:\.\d+)?)px/i', $header, $position)) {
                $logoX += (float) $position[1] * 0.75;
                $logoTop -= (float) $position[2] * 0.75;
            }
            $logoWidth = min($logoWidth, $this->contentWidth); $logoHeight = $logoWidth * 140 / 429;
            $this->content .= 'q ' . $logoWidth . ' 0 0 ' . $logoHeight . ' ' . $logoX . ' ' . ($logoTop - $logoHeight) . " cm /Logo Do Q\n";
            $headerBottom = min($headerBottom, $logoTop - $logoHeight);
        }
        $header = $this->drawInlineImages($header, $this->marginLeft, $top, $this->contentWidth);
        $header = $this->drawAbsoluteTextBoxes($header, $this->marginLeft, $top, $this->contentWidth);
        $header = str_replace('[[KINECT_LOGO]]', '', $header);
        $lines = $this->richTextLines($header, 90);
        $headerLines = array_slice($lines, 0, 3);
        $largeLogo = isset($logoWidth) && $logoWidth > $this->contentWidth * 0.65;
        foreach ($headerLines as $i => $line) $this->renderRichLine($line, $largeLogo ? $this->marginLeft : $this->marginLeft + 180, ($largeLogo ? $top - ($logoWidth * 140 / 429) - 12 : $top - 12) - ($i * 12), 8, $largeLogo ? $this->contentWidth : max(0, $this->contentWidth - 180));
        if ($headerLines) $headerBottom = min($headerBottom, $top - 12 - (count($headerLines) * 12));
        $this->headerBottomY = $headerBottom;
    }

    private function bodyStartY(): float
    {
        return self::PAGE_HEIGHT - $this->marginTop - 72;
    }

    private function preparePageBackground(): void
    {
        $html = (string) ($this->settings['body_html'] ?? '');
        $html = preg_replace_callback('/<img\b(?=[^>]*data-page-background=["\']1["\'])(?=[^>]*src=["\']data:image\/jpeg;base64,([^"\']+)["\'])[^>]*>/i', function ($m) {
            $data = base64_decode($m[1], true);
            $info = $data !== false ? @getimagesizefromstring($data) : false;
            if (!$info || $info['mime'] !== 'image/jpeg') return '';
            $this->pageBackgroundIndex = count($this->extraImages);
            $this->extraImages[] = ['data' => $data, 'width' => $info[0], 'height' => $info[1]];
            return '';
        }, $html) ?? $html;
        $this->settings['body_html'] = $html;
    }

    private function renderEditableBody(string $html, array $request, array $items): void
    {
        $this->cursorY = $this->bodyStartY();
        $html = $this->resolveTemplateFields($html, $request, $items);
        $html = preg_replace_callback('/<img\b[^>]*src=["\']data:image\/jpeg;base64,([^"\']+)["\'][^>]*>/i', function ($m) {
            $data = base64_decode($m[1], true);
            $info = $data !== false ? @getimagesizefromstring($data) : false;
            if (!$info || $info['mime'] !== 'image/jpeg') return '';
            $index = count($this->extraImages);
            $ratio = 1.0;
            if (preg_match('/style=["\'][^"\']*width\s*:\s*(25|50|75|100)%/i', $m[0], $size)) $ratio = (int) $size[1] / 100;
            elseif (preg_match('/style=["\'][^"\']*width\s*:\s*(\d+(?:\.\d+)?)px/i', $m[0], $size)) $ratio = ((float) $size[1] * 0.75) / max(1, $this->contentWidth);
            $image = ['data' => $data, 'width' => $info[0], 'height' => $info[1], 'ratio' => $ratio];
            if (preg_match('/style=["\'][^"\']*position\s*:\s*absolute/i', $m[0]) && preg_match('/style=["\'][^"\']*left\s*:\s*(-?\d+(?:\.\d+)?)px/i', $m[0], $left) && preg_match('/style=["\'][^"\']*top\s*:\s*(-?\d+(?:\.\d+)?)px/i', $m[0], $top)) {
                $image['left_px'] = (float) $left[1];
                $image['top_px'] = (float) $top[1];
            }
            $this->extraImages[] = $image;
            return '<div data-pdf-image="' . $index . '"></div>';
        }, $html) ?? $html;
        $html = preg_replace_callback('/<table\b[^>]*>(?:(?!<table\b).)*data-dynamic-block=["\']equipment_rows["\'](?:(?!<table\b).)*<\/table>/is', function ($m) use ($request, $items) {
            $headers = [];
            if (preg_match_all('/<th\b[^>]*>(.*?)<\/th>/is', $m[0], $hm)) $headers = array_map(fn($v) => $this->plainText($v), $hm[1]);
            return '<div data-pdf-equipment="' . base64_encode(json_encode($headers)) . '"></div>';
        }, $html) ?? $html;
        $html = preg_replace_callback('/<table\b[^>]*>(.*?)<\/table>/is', function ($m) {
            $rows = []; preg_match_all('/<tr\b[^>]*>(.*?)<\/tr>/is', $m[1], $matches);
            foreach ($matches[1] as $row) { preg_match_all('/<(td|th)\b[^>]*>(.*?)<\/\1>/is', $row, $cells); $rows[] = array_map(fn($v) => $this->plainText($v), $cells[2]); }
            return $rows ? '<div data-pdf-table="' . base64_encode(json_encode($rows)) . '"></div>' : '';
        }, $html) ?? $html;
        $blocks = preg_split('/(<(?:h[1-6]|p|div|li|img)\b[^>]*>.*?<\/(?:h[1-6]|p|div|li)>|<img\b[^>]*\/?\s*>)/is', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($blocks as $block) {
            if (preg_match('/data-pdf-image="(\d+)"/', $block, $m)) {
                $image = $this->extraImages[(int) $m[1]] ?? null;
                if ($image) {
                    $width = $this->contentWidth * ($image['ratio'] ?? 1);
                    if (($image['ratio'] ?? 1) === 1.0) $width = min($width, $image['width']);
                    $height = $width * $image['height'] / max(1, $image['width']);
                    if (isset($image['left_px'], $image['top_px'])) {
                        $x = $this->marginLeft + $image['left_px'] * 0.75;
                        $y = $this->bodyStartY() - $image['top_px'] * 0.75 - $height;
                        $this->content .= 'q 0 0 ' . self::PAGE_WIDTH . ' ' . self::PAGE_HEIGHT . ' re W n ' . $width . ' 0 0 ' . $height . ' ' . $x . ' ' . $y . ' cm /Im' . (int) $m[1] . " Do Q\n";
                        continue;
                    }
                    if ($this->cursorY - $height < $this->marginBottom + 24) { $this->startPage($this->currentRequest); $this->cursorY = $this->bodyStartY(); }
                    $this->content .= "q {$width} 0 0 {$height} {$this->marginLeft} " . ($this->cursorY - $height + 10) . " cm /Im" . (int) $m[1] . " Do Q\n";
                    $this->cursorY -= $height + 10;
                }
                continue;
            }
            if (preg_match('/data-pdf-equipment="([A-Za-z0-9+\/=]*)"/', $block, $m)) {
                $headers = json_decode(base64_decode($m[1], true) ?: '[]', true) ?: [];
                $this->writeEquipmentTable($request, $items, $headers);
                continue;
            }
            if (preg_match('/data-pdf-table="([A-Za-z0-9+\/=]*)"/', $block, $m)) {
                $rows = json_decode(base64_decode($m[1], true) ?: '[]', true) ?: [];
                $this->writeEditableTable($rows);
                continue;
            }
            if (preg_match('/data-freeform-text=["\']1["\']/', $block)) {
                $this->drawAbsoluteTextBoxes($block, $this->marginLeft, $this->bodyStartY(), $this->contentWidth);
                continue;
            }
            if (preg_match('/^<img\b/i', $block)) { continue; }
            $tag = 'p'; if (preg_match('/^<(h[1-6]|p|div|li)\b/i', $block, $m)) $tag=strtolower($m[1]);
            $lines=$this->richTextLines($block, 100); $size= str_starts_with($tag,'h') ? ($tag==='h1'?16:($tag==='h2'?12:10)) : 9; if(str_starts_with($tag,'h'))$this->cursorY-=8;
            foreach($lines as $line){if($this->cursorY<$this->marginBottom+24){$this->startPage($this->currentRequest);$this->cursorY=self::PAGE_HEIGHT-$this->marginTop-40;} $this->renderRichLine($line,$this->marginLeft,$this->cursorY,$size,$this->contentWidth);$this->cursorY-=($size+4);}
            if(in_array($tag,['p','div','li'],true))$this->cursorY-=3;
        }
    }

    private function drawAbsoluteTextBoxes(string $html, float $originX, float $originTop, float $maxWidth): string
    {
        return preg_replace_callback('/<div\b(?=[^>]*data-freeform-text=["\']1["\'])[^>]*>(.*?)<\/div>/is', function ($m) use ($originX, $originTop, $maxWidth) {
            $openingTag = substr($m[0], 0, strpos($m[0], '>') + 1);
            if (!preg_match('/style=["\']([^"\']*)["\']/i', $openingTag, $styleMatch)) return '';
            $style = $styleMatch[1];
            if (!preg_match('/position\s*:\s*absolute/i', $style) || !preg_match('/left\s*:\s*(-?\d+(?:\.\d+)?)px/i', $style, $left) || !preg_match('/top\s*:\s*(-?\d+(?:\.\d+)?)px/i', $style, $top)) return '';
            $width = $maxWidth;
            if (preg_match('/width\s*:\s*(\d+(?:\.\d+)?)px/i', $style, $size)) $width = (float) $size[1] * 0.75;
            $fontSize = 9;
            if (preg_match('/font-size\s*:\s*(\d+(?:\.\d+)?)(px|pt)/i', $style, $size)) $fontSize = (float) $size[1] * (strtolower($size[2]) === 'px' ? 0.75 : 1);
            $drawX = $originX + (float) $left[1] * 0.75;
            $drawTop = $originTop - (float) $top[1] * 0.75;
            $maxChars = max(1, (int) floor($width / max(1, $fontSize * 0.48)));
            $lines = $this->richTextLines($m[1], $maxChars);
            foreach ($lines as $index => $line) $this->renderRichLine($line, $drawX, $drawTop - $fontSize - ($index * ($fontSize + 3)), $fontSize, $width);
            return '';
        }, $html) ?? $html;
    }

    private function drawInlineImages(string $html, float $x, float $top, float $maxWidth): string
    {
        return preg_replace_callback('/<img\b[^>]*src=["\']data:image\/jpeg;base64,([^"\']+)["\'][^>]*>/i', function ($m) use ($x, $top, $maxWidth) {
            $data = base64_decode($m[1], true);
            $info = $data !== false ? @getimagesizefromstring($data) : false;
            if (!$info || $info['mime'] !== 'image/jpeg') return '';
            $index = count($this->extraImages);
            $this->extraImages[] = ['data' => $data, 'width' => $info[0], 'height' => $info[1]];
            $ratio = 1.0;
            if (preg_match('/style=["\'][^"\']*width\s*:\s*(25|50|75|100)%/i', $m[0], $size)) $ratio = (int) $size[1] / 100;
            elseif (preg_match('/style=["\'][^"\']*width\s*:\s*(\d+(?:\.\d+)?)px/i', $m[0], $size)) $ratio = ((float) $size[1] * 0.75) / max(1, $maxWidth);
            $width = $maxWidth * $ratio;
            if ($ratio === 1.0 && !preg_match('/style=["\'][^"\']*width\s*:/i', $m[0])) $width = min($width, $info[0]);
            $height = $width * $info[1] / max(1, $info[0]);
            $drawX = $x;
            $drawTop = $top;
            if (preg_match('/style=["\'][^"\']*position\s*:\s*absolute/i', $m[0]) && preg_match('/style=["\'][^"\']*left\s*:\s*(-?\d+(?:\.\d+)?)px/i', $m[0], $left) && preg_match('/style=["\'][^"\']*top\s*:\s*(-?\d+(?:\.\d+)?)px/i', $m[0], $offsetTop)) {
                $drawX += (float) $left[1] * 0.75;
                $drawTop -= (float) $offsetTop[1] * 0.75;
            }
            $this->content .= "q {$width} 0 0 {$height} {$drawX} " . ($drawTop - $height) . " cm /Im{$index} Do Q\n";
            return '';
        }, $html) ?? $html;
    }

    private function writeEditableTable(array $rows): void
    {
        if (!$rows) return;
        $columnCount = max(array_map('count', $rows));
        $widths = array_fill(0, $columnCount, (int) ($this->contentWidth / $columnCount));
        $widths[$columnCount - 1] += (int) $this->contentWidth - array_sum($widths);
        foreach ($rows as $index => $row) {
            $cells = array_map(fn($value) => $this->wrap((string) $value, 20), $row);
            $cells = array_pad($cells, $columnCount, ['']);
            $height = max(26, max(array_map('count', $cells)) * 10 + 8);
            if ($this->cursorY - $height < $this->marginBottom + 24) { $this->startPage($this->currentRequest); $this->cursorY = $this->bodyStartY(); }
            $this->drawTableRow((int) $this->marginLeft, $this->cursorY, $height, $widths, $cells, $index === 0);
            $this->cursorY -= $height;
        }
        $this->cursorY -= 8;
    }
    private function writeEmployeeInfo(array $r): void
    {
        $y = 860;
        $rows = [
            ['Employee/Accountable Personnel Name: ' . ($r['requestor_name'] ?? ''), 'Date: ' . ($r['form_date'] ?? '')],
            ['Department: ' . ($r['department'] ?? ''), 'Mobile: ' . ($r['mobile'] ?? '')],
            ['Address: ' . ($r['address'] ?? ''), 'Email: ' . ($r['email'] ?? '')],
        ];
        foreach ($rows as $row) {
            $left = $this->wrap($row[0], 58); $right = $this->wrap($row[1], 35);
            $count = max(count($left), count($right));
            for ($i=0; $i<$count; $i++) {
                $this->text(48, $y, $left[$i] ?? '', 8.5);
                $this->text(330, $y, $right[$i] ?? '', 8.5);
                $y -= 13;
            }
            $y -= 4;
        }
        $this->cursorY = $y - 10;
    }

    private int $cursorY = 0;

    private function writeEquipmentTable(array $request, array $items, array $templateHeaders = []): void
    {
        $widths = array_map(fn($value) => (int) round($value * $this->contentWidth / 516), [30, 74, 76, 76, 76, 76, 108]);
        $widths[count($widths) - 1] += (int) $this->contentWidth - array_sum($widths);
        $headers = $templateHeaders ?: json_decode((string) ($this->settings['equipment_headers'] ?? ''), true);
        $defaultHeaders = ['Item No.', 'Description', 'Model/Brand', 'Serial Number', 'Employee', 'Remarks', 'Date Issued'];
        if (!is_array($headers) || count($headers) !== 7) $headers = $defaultHeaders;
        $headers = array_map('strval', $headers);
        $this->drawTableRow((int) $this->marginLeft, $this->cursorY, 28, $widths, array_map(fn ($h) => $this->wrap($h, 16), $headers), true);
        $this->cursorY -= 28;

        foreach ($items as $index => $item) {
            $cells = [
                [(string) ($index + 1)],
                $this->wrap((string) ($item['description'] ?? ''), 9),
                $this->wrap((string) ($item['model_brand'] ?? ''), 11),
                $this->wrap((string) ($item['serial_number'] ?? ''), 11),
                $this->wrap((string) ($item['employee'] ?? ''), 11),
                $this->wrap((string) ($item['remarks'] ?? ''), 11),
                $this->wrap((string) ($item['item_date'] ?? ''), 11),
            ];
            $height = max(24, max(array_map('count', $cells)) * 10 + 8);
            if ($this->cursorY - $height < $this->marginBottom + 24) {
                $this->startPage($request);
                $this->cursorY = $this->bodyStartY();
                $this->drawTableRow((int) $this->marginLeft, $this->cursorY, 28, $widths, array_map(fn ($h) => $this->wrap($h, 16), $headers), true);
                $this->cursorY -= 28;
            }
            $this->drawTableRow((int) $this->marginLeft, $this->cursorY, $height, $widths, $cells, false);
            $this->cursorY -= $height;
        }
        $this->cursorY -= 18;
    }

    private function writeAcknowledgment(array $r): void
    {
        $lines = $this->richTextLines((string) ($this->settings['acknowledgment'] ?? ''), 115);
        if ($this->cursorY < 170) { $this->startPage($r); $this->cursorY = 910; }
        $this->text(48, $this->cursorY, 'Acknowledgment', 10, true);
        $this->cursorY -= 18;
        foreach ($lines as $line) {
            if ($this->cursorY < 125) { $this->startPage($r); $this->cursorY = 910; }
            $this->renderRichLine($line, 48, $this->cursorY, 8);
            $this->cursorY -= 12;
        }
        if ($this->cursorY < 180) { $this->startPage($r); $this->cursorY = 910; }
        $this->cursorY -= 35;
        $this->line(48, $this->cursorY, 210, $this->cursorY);
        $this->line(225, $this->cursorY, 382, $this->cursorY);
        $this->line(397, $this->cursorY, 564, $this->cursorY);
        $this->text(48, $this->cursorY - 14, 'Signature over Printed Name', 7);
        $this->text(48, $this->cursorY - 25, (string) ($r['signature_name'] ?? ''), 8, true);
        $this->text(225, $this->cursorY - 14, 'Verified by (IT Specialist)', 7);
        $this->text(225, $this->cursorY - 25, (string) ($r['verified_by'] ?? ''), 8, true);
        $this->text(397, $this->cursorY - 14, 'Approved by (Supervisor/President)', 7);
        $this->text(397, $this->cursorY - 25, (string) ($r['approved_by'] ?? ''), 7, true);
    }

    private function drawTableRow(int $x, int $top, int $height, array $widths, array $cells, bool $header): void
    {
        $totalWidth = array_sum($widths);
        if ($header) $this->content .= "0.91 g {$x} " . ($top - $height) . " {$totalWidth} {$height} re f 0 g\n";
        $this->content .= "0.65 G 0.5 w {$x} " . ($top - $height) . " {$totalWidth} {$height} re S 0 G\n";
        $colX = $x;
        foreach ($widths as $i => $width) {
            if ($i > 0) $this->line($colX, $top, $colX, $top - $height);
            $lines = $cells[$i] ?? [''];
            foreach ($lines as $lineIndex => $line) {
                $this->text($colX + 3, $top - 12 - ($lineIndex * 10), $line, $header ? 6.5 : 7, $header);
            }
            $colX += $width;
        }
    }

    private function wrap(string $text, int $limit): array
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
        if ($text === '') return [''];
        return explode("\n", wordwrap($text, $limit, "\n", true));
    }

    private function plainText(string $html): string
    {
        $text = preg_replace('/<\s*br\s*\/?>/i', "\n", $html) ?? $html;
        $text = preg_replace('/<\/(p|div|li)\s*>/i', "\n", $text) ?? $text;
        $text = preg_replace('/<li\b[^>]*>/i', '• ', $text) ?? $text;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $lines = array_map(static fn ($line) => trim(preg_replace('/[ \t]+/u', ' ', $line) ?? $line), preg_split('/\R/u', $text) ?: []);
        return trim(implode("\n", array_filter($lines, static fn ($line) => $line !== '')));
    }

    private function richTextLines(string $html, int $maxChars): array
    {
        $lines = [[]]; $lineLength = 0; $styles = ['b' => 0, 'i' => 0, 'u' => 0]; $align = 'left';
        $append = function (string $text) use (&$lines, &$lineLength, &$styles, &$align, $maxChars): void {
            $parts = preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
            foreach ($parts as $part) {
                if (preg_match('/\R/u', $part)) { $lines[] = []; $lineLength = 0; continue; }
                $length = strlen($part);
                if ($lineLength > 0 && $lineLength + $length > $maxChars && trim($part) !== '') { $lines[] = []; $lineLength = 0; }
                $style = ['bold' => $styles['b'] > 0, 'italic' => $styles['i'] > 0, 'underline' => $styles['u'] > 0, 'align' => $align];
                $last = count($lines) - 1; $runCount = count($lines[$last]);
                if ($runCount && $lines[$last][$runCount - 1]['style'] === $style) $lines[$last][$runCount - 1]['text'] .= $part;
                else $lines[$last][] = ['text' => $part, 'style' => $style];
                $lineLength += $length;
            }
        };
        $tokens = preg_split('/(<[^>]+>)/u', $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) ?: [];
        foreach ($tokens as $token) {
            if ($token[0] !== '<') { $append(html_entity_decode($token, ENT_QUOTES | ENT_HTML5, 'UTF-8')); continue; }
            if (!preg_match('/^<\s*(\/)?\s*([a-z0-9]+)/i', $token, $m)) continue;
            $closing = ($m[1] ?? '') === '/'; $tag = strtolower($m[2]);
            if ($tag === 'br') { $lines[] = []; $lineLength = 0; continue; }
            if (in_array($tag, ['b', 'strong', 'i', 'em', 'u'], true)) {
                $key = in_array($tag, ['b', 'strong'], true) ? 'b' : (in_array($tag, ['i', 'em'], true) ? 'i' : 'u');
                $styles[$key] = max(0, $styles[$key] + ($closing ? -1 : 1));
            }
            if (!$closing && preg_match('/text-align\s*:\s*(left|center|right|justify)/i', $token, $alignMatch)) $align = strtolower($alignMatch[1]);
            if ($tag === 'li' && !$closing) $append('• ');
            if (in_array($tag, ['p', 'div', 'li'], true) && $closing) {
                if (count($lines[count($lines) - 1]) > 0) { $lines[] = []; $lineLength = 0; }
                $align = 'left';
            }
        }
        $lines = array_values(array_filter($lines, static fn ($line) => trim(implode('', array_column($line, 'text'))) !== ''));
        return $lines ?: [[['text' => '', 'style' => ['bold' => false, 'italic' => false, 'underline' => false, 'align' => 'left']]]];
    }

    private function renderRichLine(array $runs, float $x, float $y, float $size, float $width = 516): void
    {
        $lineWidth = array_sum(array_map(fn ($run) => strlen($run['text']) * $size * 0.48, $runs));
        $align = $runs[0]['style']['align'] ?? 'left';
        if ($align === 'center') $x += max(0, ($width - $lineWidth) / 2);
        elseif ($align === 'right') $x += max(0, $width - $lineWidth);
        foreach ($runs as $run) {
            $style = $run['style'];
            $this->text($x, $y, $run['text'], $size, $style['bold'], $style['italic'], $style['underline']);
            $x += strlen($run['text']) * $size * 0.48;
        }
    }

    private function text(float $x, float $y, string $value, float $size = 10, bool $bold = false, bool $italic = false, bool $underline = false): void
    {
        $encoded = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $value);
        if ($encoded === false) $encoded = $value;
        $encoded = str_replace(["\\", '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ''], $encoded);
        $font = $bold ? ($italic ? 'F4' : 'F2') : ($italic ? 'F3' : 'F1');
        $this->content .= "BT /{$font} {$size} Tf {$x} {$y} Td ({$encoded}) Tj ET\n";
        if ($underline) $this->line($x, $y - 1, $x + (strlen($value) * $size * 0.48), $y - 1);
    }

    private function line(float $x1, float $y1, float $x2, float $y2): void
    {
        $this->content .= "0.35 G 0.5 w {$x1} {$y1} m {$x2} {$y2} l S 0 G\n";
    }

    private function finishPage(): void
    {
        $footerY = $this->marginBottom - 3;
        $this->line($this->marginLeft, $footerY, self::PAGE_WIDTH - $this->marginRight, $footerY);
        $this->text(self::PAGE_WIDTH - $this->marginRight - 90, $footerY + 9, 'RCN: ' . (string) ($this->currentRequest['document_number'] ?? ''), 7, true);
        $footer = $this->resolveTemplateFields((string) ($this->settings['footer_text'] ?? ''), $this->currentRequest, $this->currentItems);
        $footer = $this->drawInlineImages($footer, $this->marginLeft, $footerY - 8, min(160, $this->contentWidth));
        $footer = $this->drawAbsoluteTextBoxes($footer, $this->marginLeft, $footerY - 8, min(160, $this->contentWidth));
        foreach (array_slice($this->richTextLines($footer, 100), 0, 3) as $i => $line) $this->renderRichLine($line, $this->marginLeft, $footerY - 15 - ($i * 10), 7);
        $this->pages[] = $this->content;
        $this->content = '';
    }

    /** Replace editor field tags in every document region with the submitted request values. */
    private function resolveTemplateFields(string $html, array $request, array $items = []): string
    {
        $aliases = [
            'employee_name' => 'requestor_name',
            'accountableperson' => 'requestor_name',
            'accountable_person' => 'requestor_name',
            'date' => 'form_date',
            'document_no' => 'document_number',
        ];
        $valueFor = static function (string $key) use ($request, $items, $aliases): ?string {
            $key = strtolower(trim($key));
            if ($key === 'kinect_logo') return '[[KINECT_LOGO]]';
            if ($key === 'page_number') return 'DOCUMENTPAGENUMBERPLACEHOLDER';
            if ($key === 'total_pages') return 'DOCUMENTPAGECOUNTPLACEHOLDER';
            if ($key === 'employee') {
                $employees = array_values(array_unique(array_filter(array_map(static fn ($item) => trim((string) ($item['employee'] ?? '')), $items))));
                return implode(', ', $employees);
            }
            $key = $aliases[$key] ?? $key;
            $allowed = ['document_number', 'user', 'requestor_name', 'form_date', 'department', 'mobile', 'address', 'email', 'signature_name', 'verified_by', 'approved_by'];
            return in_array($key, $allowed, true) ? (string) ($request[$key] ?? '') : null;
        };
        $html = preg_replace_callback('/<span\b[^>]*data-template-field=["\']([^"\']+)["\'][^>]*>.*?<\/span>/is', static function ($match) use ($valueFor) {
            $value = $valueFor($match[1]);
            return $value === null ? $match[0] : htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }, $html) ?? $html;
        return preg_replace_callback('/\{\{\s*([a-z_]+)\s*\}\}|\[\[\s*([a-z_]+)\s*\]\]|(?<![a-z0-9_@])@([a-z][a-z0-9_]*)/i', static function ($match) use ($valueFor) {
            $key = $match[1] !== '' ? $match[1] : ($match[2] !== '' ? $match[2] : $match[3]);
            $value = $valueFor($key);
            return $value === null ? $match[0] : htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }, $html) ?? $html;
    }

    private function buildPdf(): string
    {
        $count = count($this->pages);
        $pageBase = 8 + count($this->extraImages);
        $xObjects = '/Logo 7 0 R';
        foreach ($this->extraImages as $i => $unused) $xObjects .= ' /Im' . $i . ' ' . (8 + $i) . ' 0 R';
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Kids [' . implode(' ', array_map(fn ($i) => ($pageBase + $i * 2) . ' 0 R', range(0, $count - 1))) . '] /Count ' . $count . ' >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
            4 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>',
            5 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Oblique /Encoding /WinAnsiEncoding >>',
            6 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-BoldOblique /Encoding /WinAnsiEncoding >>',
            7 => $this->logoImageObject(),
        ];
        foreach ($this->extraImages as $i => $image) {
            $objects[8 + $i] = '<< /Type /XObject /Subtype /Image /Width ' . $image['width'] . ' /Height ' . $image['height'] . ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen($image['data']) . ">>\nstream\n" . $image['data'] . "\nendstream";
        }
        foreach ($this->pages as $i => $content) {
            $content = str_replace(['DOCUMENTPAGENUMBERPLACEHOLDER', 'DOCUMENTPAGECOUNTPLACEHOLDER'], [(string) ($i + 1), (string) $count], $content);
            $pageId = $pageBase + $i * 2;
            $streamId = $pageId + 1;
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . self::PAGE_WIDTH . ' ' . self::PAGE_HEIGHT . '] /Resources << /Font << /F1 3 0 R /F2 4 0 R /F3 5 0 R /F4 6 0 R >> /XObject << ' . $xObjects . ' >> >> /Contents ' . $streamId . ' 0 R >>';
            $objects[$streamId] = "<< /Length " . strlen($content) . ">>\nstream\n" . $content . "endstream";
        }
        ksort($objects);
        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [0];
        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }
        $xref = strlen($pdf);
        $maxId = max(array_keys($objects));
        $pdf .= "xref\n0 " . ($maxId + 1) . "\n0000000000 65535 f \n";
        for ($id = 1; $id <= $maxId; $id++) $pdf .= sprintf('%010d 00000 n ', $offsets[$id]) . "\n";
        $pdf .= "trailer\n<< /Size " . ($maxId + 1) . " /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
        return $pdf;
    }

    private function logoImageObject(): string
    {
        $path = defined('FCPATH') ? FCPATH . 'assets/images/accountability-letterhead.jpg' : '';
        $image = $path !== '' && is_file($path) ? file_get_contents($path) : false;
        if ($image === false) return '<< /Type /XObject /Subtype /Image /Width 1 /Height 1 /ColorSpace /DeviceRGB /BitsPerComponent 8 /Length 3 >> stream\n' . "\xFF\xFF\xFF" . "\nendstream";
        return '<< /Type /XObject /Subtype /Image /Width 429 /Height 140 /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' . strlen($image) . ">>\nstream\n" . $image . "\nendstream";
    }
}
