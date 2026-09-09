<?php
declare(strict_types=1);

/**
 * Export the existing V1 saved results as four editable PowerPoint slides.
 *
 * Adapts Shawn Kelley's CustomerPresentation OOXML layout/package helpers.
 * V1 deliberately retains its own data contract, prices and calculation rules.
 * This class only formats saved decimal strings. It never calls GenAI, supplies
 * catalog prices, or recalculates totals. No Composer or external service needed.
 */
final class ResultsPresentation
{
    private const A = 'http://schemas.openxmlformats.org/drawingml/2006/main';
    private const P = 'http://schemas.openxmlformats.org/presentationml/2006/main';
    private const R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    private const DISCLOSURE = 'Demonstration only. Subscription costs, not a quote, licensing determination, equivalence assessment or full TCO.';
    private int $shapeId = 1;

    /**
     * @param array<string,mixed> $comparison Saved comparison and rule metadata.
     * @param array<string,mixed> $result Saved comparison_result row.
     * @param list<array<string,mixed>> $lines Reviewed lines in the same snapshot.
     * @return string Complete PPTX bytes, ready for an attachment response.
     */
    public function export(array $comparison, array $result, array $lines): string
    {
        if (($comparison['status'] ?? '') !== 'CALCULATED'
            || (string) ($comparison['id'] ?? '') === ''
            || (string) ($comparison['id'] ?? '') !== (string) ($result['comparison_id'] ?? '')) {
            throw new DomainException('Calculate this comparison before downloading PowerPoint.');
        }
        $rows = [['Period (USD)', 'RHEL', 'Oracle Linux', 'Difference']];
        foreach (['annual' => 'Annual', 'three_year' => 'Three years', 'five_year' => 'Five years'] as $period => $label) {
            $rows[] = [$label, $this->amount($result['rhel_' . $period . '_total'] ?? null),
                $this->amount($result['oracle_linux_' . $period . '_total'] ?? null),
                $this->amount($result[$period . '_difference'] ?? null, true)];
        }
        $counts = ['RHEL' => 0, 'ORACLE_LINUX' => 0];
        $excluded = 0;
        $groups = [];
        foreach ($lines as $line) {
            if (($line['review_status'] ?? '') === 'EXCLUDED') {
                ++$excluded;
                continue;
            }
            $side = $line['input_side'] ?? '';
            $group = $line['comparison_group'] ?? null;
            if (($line['review_status'] ?? '') !== 'CONFIRMED' || !array_key_exists($side, $counts)
                || !is_numeric($group) || (int) $group < 1) {
                throw new DomainException('Review and calculate the comparison again before exporting.');
            }
            ++$counts[$side];
            $groups[(int) $group][$side] = true;
        }
        if (!$counts['RHEL'] || !$counts['ORACLE_LINUX']) {
            throw new DomainException('Both sides need confirmed lines.');
        }
        foreach ($groups as $sides) {
            if (count($sides) !== 2) {
                throw new DomainException('Every included group needs both sides.');
            }
        }
        $name = $this->clean((string) ($comparison['name'] ?? 'Comparison'));
        $rule = $this->clean((string) ($comparison['version_label'] ?? ''));
        $date = $this->clean((string) ($result['calculated_at'] ?? ''));
        $trace = 'Comparison ' . $comparison['id'] . "\nRule: " . $rule . "\nCalculated: " . $date . ' (database time)';
        $slides = [
            $this->heading('Oracle Linux Value Navigator')
                . $this->box($this->shorten($name, 90), .6, 1.8, 12, 1.6, 30, true, 'B84032')
                . $this->box('Confirmed subscription-cost comparison', .6, 3.65, 12, .7, 24)
                . $this->box($trace, .6, 4.8, 12, 1.5, 18),
            $this->heading('Subscription-cost results')
                . $this->table($rows, .6, 1.7, [2.1, 3.0, 3.0, 3.95], .85, 18)
                . $this->box('Difference = RHEL minus Oracle Linux. A negative difference means Oracle Linux costs more.', .6, 5.55, 12, 1, 20),
            $this->heading('Representative review')
                . $this->table([
                    ['Review measure', 'Count'],
                    ['Confirmed RHEL lines', (string) $counts['RHEL']],
                    ['Confirmed Oracle Linux lines', (string) $counts['ORACLE_LINUX']],
                    ['Aligned comparison groups', (string) count($groups)],
                    ['Excluded lines (not in totals)', (string) $excluded],
                ], .6, 1.6, [9.05, 3.0], .75, 20)
                . $this->box('The CSV workbook contains the full source inputs, reviewed lines and notes.', .6, 5.7, 12, .8, 20),
            $this->heading('Assumptions and next steps')
                . $this->box("Three- and five-year totals repeat the annual costs.\nNo escalation, discounting or currency conversion.\nMigration, hardware, services and tax are outside this comparison.\nReview quantities, prices and scope before sharing results.\nUse demonstration data only.", .6, 1.65, 12, 3.75, 23)
                . $this->box('Retain the CSV workbook with this presentation for traceability.', .6, 5.8, 12, .7, 20),
        ];
        return $this->package($slides, ['notes' => $name . "\n\n" . $trace . "\n\n" . self::DISCLOSURE
            . "\nThe presentation summarizes saved results. Use the CSV export for complete evidence."]);
    }

    /** Preserve cents and sign exactly without floating-point conversion. */
    private function amount(mixed $value, bool $signed = false): string
    {
        if (!is_string($value) || !preg_match($signed ? '/^-?\\d{1,14}\\.\\d{2}$/D' : '/^\\d{1,14}\\.\\d{2}$/D', $value)) {
            throw new DomainException('Saved totals are missing or invalid. Calculate the comparison again.');
        }
        [$whole, $cents] = explode('.', $value);
        return preg_replace('/\\B(?=(\\d{3})+(?!\\d))/', ',', $whole) . '.' . $cents;
    }

    /** Replace invalid XML characters, keeping user text as inert slide text. */
    private function clean(string $text): string
    {
        $text = htmlspecialchars_decode(htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), ENT_QUOTES);
        return preg_replace('/[^\\x{9}\\x{A}\\x{D}\\x{20}-\\x{D7FF}\\x{E000}-\\x{FFFD}\\x{10000}-\\x{10FFFF}]/u', '', $text) ?? '';
    }

    /** Shorten only display text; full comparison name remains in speaker notes. */
    private function shorten(string $text, int $limit): string
    {
        $characters = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        return count($characters) <= $limit ? $text : implode('', array_slice($characters, 0, $limit - 3)) . '...';
    }

    /**
     * Package OOXML with libzip through PHP's ZipArchive extension.
     * Microsoft's packaging reader rejects the version-needed 0 headers emitted
     * by the previous PharData writer. ZipArchive writes standard ZIP headers.
     * The private temporary directory is removed on both success and failure.
     */
    private function package(array $slides, array $data): string
    {
        if (!class_exists('ZipArchive')) {
            throw new RuntimeException('PHP ZipArchive is required. Complete Lab 5 Task 2.');
        }
        $directory = sys_get_temp_dir() . '/olvn-ppt-' . bin2hex(random_bytes(16));
        if (!mkdir($directory, 0700)) {
            throw new RuntimeException('Cannot allocate presentation output.');
        }
        $path = $directory . '/presentation.zip';
        try {
            $archive = new ZipArchive();
            if ($archive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Cannot open presentation archive.');
            }
            foreach ($this->parts($slides, $data) as $name => $xml) {
                if (!$archive->addFromString($name, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $xml)
                    || !$archive->setCompressionName($name, ZipArchive::CM_DEFLATE)) {
                    throw new RuntimeException('Cannot write presentation archive.');
                }
            }
            if (!$archive->close()) {
                throw new RuntimeException('Cannot finalize presentation archive.');
            }
            chmod($path, 0600);
            unset($archive);
            $bytes = file_get_contents($path);
            if ($bytes === false) {
                throw new RuntimeException('Cannot read presentation output.');
            }
            return $bytes;
        } finally {
            unset($archive);
            if (is_file($path)) { unlink($path); }
            rmdir($directory);
        }
    }

    /** Build editable slides, notes, masters, theme and internal relationships. */
    private function heading(string $title): string
    {
        return $this->box($title, .6, .5, 12.1, .85, 32, true);
    }

    private function box(string $text, float $x, float $y, float $w, float $h, int $size, bool $bold = false, string $color = '282923', bool $notesBody = false): string
    {
        $id = ++$this->shapeId;
        $paragraphs = $this->paragraphs($text, $size, $bold, $color);
        return '<p:sp><p:nvSpPr><p:cNvPr id="' . $id . '" name="Text ' . $id . '"/><p:cNvSpPr txBox="1"/><p:nvPr>' . ($notesBody ? '<p:ph type="body" idx="1"/>' : '') . '</p:nvPr></p:nvSpPr>'
            . '<p:spPr><a:xfrm><a:off x="' . $this->emu($x) . '" y="' . $this->emu($y) . '"/><a:ext cx="' . $this->emu($w) . '" cy="' . $this->emu($h) . '"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom><a:noFill/></p:spPr>'
            . '<p:txBody><a:bodyPr wrap="square" lIns="0" rIns="0" tIns="0" bIns="0"/><a:lstStyle/>' . $paragraphs . '</p:txBody></p:sp>';
    }

    private function paragraphs(string $text, int $size, bool $bold = false, string $color = '282923'): string
    {
        $xml = '';
        foreach (explode("\n", $text) as $line) {
            $xml .= '<a:p><a:pPr/><a:r><a:rPr lang="en-US" sz="' . ($size * 100) . '" b="' . ($bold ? '1' : '0') . '"><a:solidFill><a:srgbClr val="' . $color . '"/></a:solidFill><a:latin typeface="Arial"/></a:rPr><a:t xml:space="preserve">' . $this->xml($line) . '</a:t></a:r><a:endParaRPr lang="en-US" sz="' . ($size * 100) . '"/></a:p>';
        }
        return $xml;
    }

    /** @param list<list<string>> $rows
     * @param list<float> $widths
     */
    private function table(array $rows, float $x, float $y, array $widths, float $rowHeight, int $size): string
    {
        $id = ++$this->shapeId;
        $xml = '<p:graphicFrame><p:nvGraphicFramePr><p:cNvPr id="' . $id . '" name="Comparison table"/><p:cNvGraphicFramePr/><p:nvPr/></p:nvGraphicFramePr><p:xfrm><a:off x="' . $this->emu($x) . '" y="' . $this->emu($y) . '"/><a:ext cx="' . $this->emu(array_sum($widths)) . '" cy="' . $this->emu(count($rows) * $rowHeight) . '"/></p:xfrm><a:graphic><a:graphicData uri="' . 'http://schemas.openxmlformats.org/drawingml/2006/table"><a:tbl><a:tblPr firstRow="1" bandRow="0"/><a:tblGrid>';
        foreach ($widths as $width) {
            $xml .= '<a:gridCol w="' . $this->emu($width) . '"/>';
        }
        $xml .= '</a:tblGrid>';
        foreach ($rows as $index => $row) {
            $xml .= '<a:tr h="' . $this->emu($rowHeight) . '">';
            foreach ($row as $cell) {
                $xml .= '<a:tc><a:txBody><a:bodyPr/><a:lstStyle/>' . $this->paragraphs($cell, $size, $index === 0, $index === 0 ? 'FFFFFF' : '282923') . '</a:txBody><a:tcPr marL="100000" marR="100000" marT="65000" marB="50000"><a:solidFill><a:srgbClr val="' . ($index === 0 ? '384239' : ($index % 2 === 0 ? 'EEEAE1' : 'F8F5EF')) . '"/></a:solidFill></a:tcPr></a:tc>';
            }
            $xml .= '</a:tr>';
        }
        return $xml . '</a:tbl></a:graphicData></a:graphic></p:graphicFrame>';
    }

    private function emu(float $inches): int
    {
        return (int) round($inches * 914400);
    }

    private function xml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function parts(array $slides, array $data): array
    {
        $parts = [];
        $overrides = '';
        $ids = '';
        $presentationRels = [['slideMaster', 'slideMasters/slideMaster1.xml'], ['notesMaster', 'notesMasters/notesMaster1.xml']];
        foreach ($slides as $index => $content) {
            $number = $index + 1;
            $ids .= '<p:sldId id="' . (256 + $index) . '" r:id="rId' . ($index + 3) . '"/>';
            $presentationRels[] = ['slide', 'slides/slide' . $number . '.xml'];
            $content .= $this->box(self::DISCLOSURE, .6, 7.05, 11.6, .32, 10);
            $content .= $this->box((string) $number, 12.4, 7.03, .35, .3, 11);
            $parts['ppt/slides/slide' . $number . '.xml'] = '<p:sld xmlns:a="' . self::A . '" xmlns:r="' . self::R . '" xmlns:p="' . self::P . '"><p:cSld><p:bg><p:bgPr><a:solidFill><a:srgbClr val="F8F5EF"/></a:solidFill><a:effectLst/></p:bgPr></p:bg>' . $this->tree($content) . '</p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sld>';
            $parts['ppt/slides/_rels/slide' . $number . '.xml.rels'] = $this->rels([['slideLayout', '../slideLayouts/slideLayout1.xml'], ['notesSlide', '../notesSlides/notesSlide' . $number . '.xml']]);
            $notes = $data['notes'];
            $parts['ppt/notesSlides/notesSlide' . $number . '.xml'] = '<p:notes xmlns:a="' . self::A . '" xmlns:r="' . self::R . '" xmlns:p="' . self::P . '"><p:cSld>' . $this->tree($this->box($notes, .5, 1, 6, 8, 11, notesBody: true)) . '</p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:notes>';
            $parts['ppt/notesSlides/_rels/notesSlide' . $number . '.xml.rels'] = $this->rels([['notesMaster', '../notesMasters/notesMaster1.xml'], ['slide', '../slides/slide' . $number . '.xml']]);
            $overrides .= $this->override('/ppt/slides/slide' . $number . '.xml', 'slide') . $this->override('/ppt/notesSlides/notesSlide' . $number . '.xml', 'notesSlide');
        }
        $parts['_rels/.rels'] = $this->rels([['officeDocument', 'ppt/presentation.xml']]);
        $parts['ppt/_rels/presentation.xml.rels'] = $this->rels($presentationRels);
        $parts['ppt/presentation.xml'] = '<p:presentation xmlns:a="' . self::A . '" xmlns:r="' . self::R . '" xmlns:p="' . self::P . '"><p:sldMasterIdLst><p:sldMasterId id="2147483648" r:id="rId1"/></p:sldMasterIdLst><p:notesMasterIdLst><p:notesMasterId r:id="rId2"/></p:notesMasterIdLst><p:sldIdLst>' . $ids . '</p:sldIdLst><p:sldSz cx="12192000" cy="6858000"/><p:notesSz cx="6858000" cy="9144000"/></p:presentation>';
        $map = '<p:clrMap accent1="accent1" accent2="accent2" accent3="accent3" accent4="accent4" accent5="accent5" accent6="accent6" bg1="lt1" bg2="lt2" folHlink="folHlink" hlink="hlink" tx1="dk1" tx2="dk2"/>';
        $parts['ppt/slideMasters/slideMaster1.xml'] = '<p:sldMaster xmlns:a="' . self::A . '" xmlns:r="' . self::R . '" xmlns:p="' . self::P . '"><p:cSld>' . $this->tree('') . '</p:cSld>' . $map . '<p:sldLayoutIdLst><p:sldLayoutId id="2147483649" r:id="rId1"/></p:sldLayoutIdLst><p:txStyles/></p:sldMaster>';
        $parts['ppt/slideMasters/_rels/slideMaster1.xml.rels'] = $this->rels([['slideLayout', '../slideLayouts/slideLayout1.xml'], ['theme', '../theme/theme1.xml']]);
        $parts['ppt/slideLayouts/slideLayout1.xml'] = '<p:sldLayout xmlns:a="' . self::A . '" xmlns:r="' . self::R . '" xmlns:p="' . self::P . '" type="blank" preserve="1"><p:cSld name="Blank">' . $this->tree('') . '</p:cSld><p:clrMapOvr><a:masterClrMapping/></p:clrMapOvr></p:sldLayout>';
        $parts['ppt/slideLayouts/_rels/slideLayout1.xml.rels'] = $this->rels([['slideMaster', '../slideMasters/slideMaster1.xml']]);
        $parts['ppt/notesMasters/notesMaster1.xml'] = '<p:notesMaster xmlns:a="' . self::A . '" xmlns:r="' . self::R . '" xmlns:p="' . self::P . '"><p:cSld>' . $this->tree('') . '</p:cSld>' . $map . '<p:notesStyle/></p:notesMaster>';
        // PowerPoint requires the notes master's theme to be a separate part
        // from the slide master's theme, even when their XML is identical.
        // Sharing theme1 passed schema validation but triggered Office repair.
        $parts['ppt/notesMasters/_rels/notesMaster1.xml.rels'] = $this->rels([['theme', '../theme/theme2.xml']]);
        $colors = '';
        foreach (['dk1' => '282923', 'lt1' => 'FFFFFF', 'dk2' => '384239', 'lt2' => 'F8F5EF', 'accent1' => 'B84032', 'accent2' => '384239', 'accent3' => '8B6A47', 'accent4' => '657C72', 'accent5' => 'BEA57A', 'accent6' => '6E6B63', 'hlink' => '0563C1', 'folHlink' => '954F72'] as $key => $color) {
            $colors .= '<a:' . $key . '><a:srgbClr val="' . $color . '"/></a:' . $key . '>';
        }
        $font = '<a:latin typeface="Arial"/><a:ea typeface=""/><a:cs typeface=""/>';
        $parts['ppt/theme/theme1.xml'] = '<a:theme xmlns:a="' . self::A . '" name="Customer comparison"><a:themeElements><a:clrScheme name="Warm neutral">' . $colors . '</a:clrScheme><a:fontScheme name="Arial"><a:majorFont>' . $font . '</a:majorFont><a:minorFont>' . $font . '</a:minorFont></a:fontScheme><a:fmtScheme name="Simple"><a:fillStyleLst>' . str_repeat('<a:solidFill><a:schemeClr val="phClr"/></a:solidFill>', 3) . '</a:fillStyleLst><a:lnStyleLst>' . str_repeat('<a:ln w="9525"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill><a:prstDash val="solid"/></a:ln>', 3) . '</a:lnStyleLst><a:effectStyleLst>' . str_repeat('<a:effectStyle><a:effectLst/></a:effectStyle>', 3) . '</a:effectStyleLst><a:bgFillStyleLst>' . str_repeat('<a:solidFill><a:schemeClr val="phClr"/></a:solidFill>', 3) . '</a:bgFillStyleLst></a:fmtScheme></a:themeElements></a:theme>';
        $parts['ppt/theme/theme2.xml'] = $parts['ppt/theme/theme1.xml'];
        foreach (['presentation' => 'presentation.main', 'slideMasters/slideMaster1' => 'slideMaster', 'slideLayouts/slideLayout1' => 'slideLayout', 'notesMasters/notesMaster1' => 'notesMaster'] as $name => $type) {
            $overrides .= $this->override('/ppt/' . $name . '.xml', $type);
        }
        $parts['[Content_Types].xml'] = '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/>' . $overrides . '<Override PartName="/ppt/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/><Override PartName="/ppt/theme/theme2.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/></Types>';
        return $parts;
    }

    private function tree(string $content): string
    {
        return '<p:spTree><p:nvGrpSpPr><p:cNvPr id="1" name=""/><p:cNvGrpSpPr/><p:nvPr/></p:nvGrpSpPr><p:grpSpPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="0" cy="0"/><a:chOff x="0" y="0"/><a:chExt cx="0" cy="0"/></a:xfrm></p:grpSpPr>' . $content . '</p:spTree>';
    }

    /** @param list<array{string,string}> $relationships */
    private function rels(array $relationships): string
    {
        $xml = '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        foreach ($relationships as $index => [$type, $target]) {
            $xml .= '<Relationship Id="rId' . ($index + 1) . '" Type="' . self::R . '/' . $type . '" Target="' . $this->xml($target) . '"/>';
        }
        return $xml . '</Relationships>';
    }

    private function override(string $path, string $type): string
    {
        return '<Override PartName="' . $path . '" ContentType="application/vnd.openxmlformats-officedocument.presentationml.' . $type . '+xml"/>';
    }
}
