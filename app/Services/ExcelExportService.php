<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExportService
{
    /**
     * Xuất dữ liệu ra file Excel (.xls - SpreadsheetML) với định dạng bảng tính cao cấp.
     *
     * @param  string  $filename  Tên file khi tải về (không cần đuôi hoặc có đuôi .xls)
     * @param  string  $title  Tiêu đề lớn hiển thị ở đầu bảng tính
     * @param  array<string>  $headers  Mảng nhãn tiêu đề các cột
     * @param  array<array<mixed>>  $rows  Mảng các dòng dữ liệu
     * @param  array<string, mixed>  $options  Tùy biến theme, cột, độ rộng, tổng kết
     */
    public function download(
        string $filename,
        string $title,
        array $headers,
        array $rows,
        array $options = []
    ): StreamedResponse {
        if (! str_ends_with(strtolower($filename), '.xls')) {
            $filename .= '.xls';
        }

        $theme = $options['theme'] ?? 'admin'; // 'admin' (Dark Slate) hoặc 'seller' (Coral Red)
        $subtitle = $options['subtitle'] ?? 'Xuất ngày '.now()->format('d/m/Y H:i:s').' - Hệ thống ShopMart';
        $sheetName = $this->sanitizeXml($options['sheet_name'] ?? 'Bảng dữ liệu');
        $columnConfigs = $options['columns'] ?? [];
        $totals = $options['totals'] ?? null;

        // Tự động tính toán độ rộng cột nếu không chỉ định
        $columnWidths = $this->calculateColumnWidths($headers, $rows, $columnConfigs);

        $response = new StreamedResponse(function () use (
            $title,
            $subtitle,
            $sheetName,
            $headers,
            $rows,
            $columnWidths,
            $columnConfigs,
            $totals,
            $theme
        ) {
            $headerBg = $theme === 'seller' ? '#ea384c' : '#1e293b';
            $headerBorder = $theme === 'seller' ? '#b91c1c' : '#0f172a';

            echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            echo '<?mso-application progid="Excel.Sheet"?>'."\n";
            ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>ShopMart E-Commerce</Author>
  <Created><?= now()->toIso8601String() ?></Created>
  <Company>ShopMart Vietnam</Company>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Borders/>
   <Font ss:FontName="Segoe UI" x:Family="Swiss" ss:Size="10" ss:Color="#334155"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="Title">
   <Font ss:FontName="Segoe UI" ss:Size="16" ss:Bold="1" ss:Color="<?= $headerBg ?>"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Subtitle">
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Italic="1" ss:Color="#64748b"/>
   <Alignment ss:Vertical="Center"/>
  </Style>
  <Style ss:ID="Header">
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="<?= $headerBg ?>" ss:Pattern="Solid"/>
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="<?= $headerBorder ?>"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#475569"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="<?= $headerBorder ?>"/>
   </Borders>
  </Style>
  <!-- Data Styles (White & Zebra Striping) -->
  <Style ss:ID="CellLeft">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <Style ss:ID="CellLeftZebra">
   <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
   <Interior ss:Color="#f8fafc" ss:Pattern="Solid"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <Style ss:ID="CellCenter">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <Style ss:ID="CellCenterZebra">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Interior ss:Color="#f8fafc" ss:Pattern="Solid"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <Style ss:ID="CellCurrency">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0\ &quot;₫&quot;"/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#0f172a"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <Style ss:ID="CellCurrencyZebra">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Interior ss:Color="#f8fafc" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0\ &quot;₫&quot;"/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#0f172a"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <Style ss:ID="CellNumber">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <NumberFormat ss:Format="#,##0"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <Style ss:ID="CellNumberZebra">
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Interior ss:Color="#f8fafc" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#e2e8f0"/><Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#f1f5f9"/></Borders>
  </Style>
  <!-- Status Badge Styles -->
  <Style ss:ID="BadgeGreen">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Bold="1" ss:Color="#166534"/>
   <Interior ss:Color="#dcfce7" ss:Pattern="Solid"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#bbf7d0"/></Borders>
  </Style>
  <Style ss:ID="BadgeAmber">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Bold="1" ss:Color="#9a3412"/>
   <Interior ss:Color="#ffedd5" ss:Pattern="Solid"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#fed7aa"/></Borders>
  </Style>
  <Style ss:ID="BadgeRed">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Bold="1" ss:Color="#991b1b"/>
   <Interior ss:Color="#fee2e2" ss:Pattern="Solid"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#fecaca"/></Borders>
  </Style>
  <Style ss:ID="BadgeBlue">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Bold="1" ss:Color="#1e40af"/>
   <Interior ss:Color="#dbeafe" ss:Pattern="Solid"/>
   <Borders><Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#bfdbfe"/></Borders>
  </Style>
  <!-- Total Summary Row -->
  <Style ss:ID="TotalLabel">
   <Font ss:FontName="Segoe UI" ss:Size="11" ss:Bold="1" ss:Color="#0f172a"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Interior ss:Color="#f1f5f9" ss:Pattern="Solid"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94a3b8"/>
    <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#475569"/>
   </Borders>
  </Style>
  <Style ss:ID="TotalCurrency">
   <Font ss:FontName="Segoe UI" ss:Size="11" ss:Bold="1" ss:Color="<?= $headerBg ?>"/>
   <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
   <Interior ss:Color="#f1f5f9" ss:Pattern="Solid"/>
   <NumberFormat ss:Format="#,##0\ &quot;₫&quot;"/>
   <Borders>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#94a3b8"/>
    <Border ss:Position="Bottom" ss:LineStyle="Double" ss:Weight="3" ss:Color="#475569"/>
   </Borders>
  </Style>
 </Styles>
 <Worksheet ss:Name="<?= $sheetName ?>">
  <Table ss:DefaultRowHeight="20">
<?php foreach ($columnWidths as $w) { ?>
   <Column ss:Width="<?= $w ?>"/>
<?php } ?>

   <!-- Title Row -->
   <Row ss:Height="28">
    <Cell ss:StyleID="Title" ss:MergeAcross="<?= max(0, count($headers) - 1) ?>">
     <Data ss:Type="String"><?= $this->sanitizeXml($title) ?></Data>
    </Cell>
   </Row>

   <!-- Subtitle Row -->
   <Row ss:Height="18">
    <Cell ss:StyleID="Subtitle" ss:MergeAcross="<?= max(0, count($headers) - 1) ?>">
     <Data ss:Type="String"><?= $this->sanitizeXml($subtitle) ?></Data>
    </Cell>
   </Row>

   <!-- Blank Spacer Row -->
   <Row ss:Height="8"/>

   <!-- Table Header Row -->
   <Row ss:Height="26">
<?php foreach ($headers as $header) { ?>
    <Cell ss:StyleID="Header">
     <Data ss:Type="String"><?= $this->sanitizeXml($header) ?></Data>
    </Cell>
<?php } ?>
   </Row>

   <!-- Data Rows -->
<?php
foreach ($rows as $rowIndex => $row) {
    $isZebra = ($rowIndex % 2 === 1);
    ?>
   <Row ss:Height="22">
<?php
    foreach ($row as $colIndex => $value) {
        $cfg = $columnConfigs[$colIndex] ?? [];
        $type = $cfg['type'] ?? 'text';
        [$styleId, $dataType, $formattedValue] = $this->resolveCellStyleAndData($value, $type, $isZebra);
        ?>
    <Cell ss:StyleID="<?= $styleId ?>">
     <Data ss:Type="<?= $dataType ?>"><?= $formattedValue ?></Data>
    </Cell>
<?php } ?>
   </Row>
<?php } ?>

   <!-- Totals Row (nếu có) -->
<?php if ($totals) { ?>
   <Row ss:Height="26">
<?php foreach ($totals as $colIndex => $totalVal) {
    $isCurrency = ($columnConfigs[$colIndex]['type'] ?? '') === 'currency';
    $style = $isCurrency ? 'TotalCurrency' : 'TotalLabel';
    $dType = is_numeric($totalVal) ? 'Number' : 'String';
    ?>
    <Cell ss:StyleID="<?= $style ?>">
     <Data ss:Type="<?= $dType ?>"><?= $this->sanitizeXml((string) $totalVal) ?></Data>
    </Cell>
<?php } ?>
   </Row>
<?php } ?>

  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <Selected/>
   <FreezePanes/>
   <FrozenNoSplit/>
   <SplitHorizontal>4</SplitHorizontal>
   <TopRowBottomPane>4</TopRowBottomPane>
   <ActivePane>2</ActivePane>
   <Panes>
    <Pane>
     <Number>3</Number>
    </Pane>
    <Pane>
     <Number>2</Number>
     <ActiveRow>0</ActiveRow>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
<?php
        }, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
            'Pragma' => 'public',
        ]);

        return $response;
    }

    /**
     * Xác định Style ID, Data Type và Format dữ liệu cho từng ô tính.
     */
    protected function resolveCellStyleAndData(mixed $value, string $type, bool $isZebra): array
    {
        if ($value === null || $value === '') {
            $style = $isZebra ? 'CellLeftZebra' : 'CellLeft';

            return [$style, 'String', ''];
        }

        switch ($type) {
            case 'currency':
                $num = is_numeric($value) ? (float) $value : 0;
                $style = $isZebra ? 'CellCurrencyZebra' : 'CellCurrency';

                return [$style, 'Number', (string) $num];

            case 'number':
                $num = is_numeric($value) ? (float) $value : 0;
                $style = $isZebra ? 'CellNumberZebra' : 'CellNumber';

                return [$style, 'Number', (string) $num];

            case 'center':
            case 'date':
                $style = $isZebra ? 'CellCenterZebra' : 'CellCenter';

                return [$style, 'String', $this->sanitizeXml((string) $value)];

            case 'status':
                $text = (string) $value;
                $lower = mb_strtolower($text);
                $badgeStyle = 'CellCenter';

                if (str_contains($lower, 'hoàn thành') || str_contains($lower, 'đã giao') || str_contains($lower, 'hoạt động') || str_contains($lower, 'active') || str_contains($lower, 'thành công')) {
                    $badgeStyle = 'BadgeGreen';
                } elseif (str_contains($lower, 'chờ') || str_contains($lower, 'đang xử lý') || str_contains($lower, 'đang giao') || str_contains($lower, 'shipping') || str_contains($lower, 'pending')) {
                    $badgeStyle = 'BadgeAmber';
                } elseif (str_contains($lower, 'hủy') || str_contains($lower, 'khóa') || str_contains($lower, 'banned') || str_contains($lower, 'inactive')) {
                    $badgeStyle = 'BadgeRed';
                } elseif (str_contains($lower, 'đã xác nhận') || str_contains($lower, 'processing')) {
                    $badgeStyle = 'BadgeBlue';
                }

                return [$badgeStyle, 'String', $this->sanitizeXml($text)];

            case 'text':
            default:
                $style = $isZebra ? 'CellLeftZebra' : 'CellLeft';

                return [$style, 'String', $this->sanitizeXml((string) $value)];
        }
    }

    /**
     * Tự động tính toán độ rộng các cột dựa theo chiều dài nội dung.
     */
    protected function calculateColumnWidths(array $headers, array $rows, array $columnConfigs): array
    {
        $widths = [];

        foreach ($headers as $colIndex => $header) {
            if (isset($columnConfigs[$colIndex]['width'])) {
                $widths[$colIndex] = $columnConfigs[$colIndex]['width'];

                continue;
            }

            // Đo độ dài lớn nhất giữa header và các dòng dữ liệu
            $maxLen = mb_strlen($header);
            $sampleRows = array_slice($rows, 0, 50); // Sample 50 dòng để tăng tốc
            foreach ($sampleRows as $row) {
                $cellVal = isset($row[$colIndex]) ? (string) $row[$colIndex] : '';
                $len = mb_strlen($cellVal);
                if ($len > $maxLen) {
                    $maxLen = $len;
                }
            }

            // Quy đổi sang points của Excel (khoảng 7-8 points / ký tự, tối thiểu 70, tối đa 260)
            $calculatedWidth = max(75, min(280, (int) ($maxLen * 8.2) + 20));
            $widths[$colIndex] = $calculatedWidth;
        }

        return $widths;
    }

    /**
     * Làm sạch ký tự đặc biệt theo chuẩn XML để tránh vỡ tài liệu.
     */
    protected function sanitizeXml(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1, 'UTF-8');
    }
}
