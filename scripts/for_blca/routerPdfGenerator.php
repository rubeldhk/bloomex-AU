<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/fpdf/fpdf.php';

class PDF extends FPDF
{

    function LoadData($file)
    {
        $lines = file($file);
        $data = array();
        foreach ($lines as $line)
            $data[] = explode(';', trim($line));
        return $data;
    }

    function BasicTable($header, $data)
    {
        $w = array(5, 15, 120, 55);
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        }
        $this->Ln();

        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR');
            $this->Cell($w[1], 6, $row[1], 'LR');
            //$this->MultiCell($w[1], 6, $row[1]);
            $this->Cell($w[2], 6, $row[2] . "\nTEST", 'LR');
            $this->Cell($w[3], 6, $row[3], 'LR');
            $this->Ln();
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }

}

class PDF_MC_Table extends FPDF
{

    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        $this->aligns = $a;
    }

    function Row($data)
    {
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = 5 * $nb;
        $this->CheckPageBreak($h);

        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w, $h);

            $this->MultiCell($w, 5, $data[$i], 0, $a);

            $this->SetXY($x + $w, $y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

}

include_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

global $mysqli;
$mysqli = new mysqli($mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db);


global $mysqli;
$route_id = (isset($_POST['route_id']) ? (int)$_POST['route_id'] : 0);

$shipping_method_names = array(
    24 => "Regular: 9:00am - 8:00pm",
    25 => " Morning: before 12:00pm",
    26 => "Evening: 6:00pm - 9:00pm"
);

$query = "SELECT 
            `r`.*,
            `d`.`service_name`,
            CONCAT(`wh_i`.`city`, ' ', `wh_i`.`street_name`, ' ', `wh_i`.`street_number`, ' ', `wh_i`.`zip`, ' ', `wh_i`.`state`, ' ', `wh_i`.`country`) AS `wh_address`,
            IF(`d`.`driver_name` = '', `d`.`service_name`, `d`.`driver_name`) AS `driver_name`,
            `wh`.`warehouse_name`,
            `r`.`id` AS 'route_id',
            `r`.`map_image`,
            STR_TO_DATE(`r`.`datetime`, '%Y-%m-%d') AS `route_date`
        FROM `jos_vm_routes` AS `r` 
        INNER JOIN `tbl_driver_option` AS `d` 
            ON 
            `d`.`id`=`r`.`driver_id`
        INNER JOIN `jos_vm_warehouse` AS `wh`
            ON
            `wh`.`warehouse_id`=`r`.`warehouse_id`
        INNER JOIN `jos_vm_warehouse_info` AS `wh_i`
            ON
            `wh_i`.`warehouse_id`=`wh`.`warehouse_id`
        WHERE 
            `r`.`id`=" . $route_id . "
        ";

$routeSql = $mysqli->query($query);


if ($routeSql->num_rows > 0) {
    $route = $routeSql->fetch_object();
    $query = "SELECT 
                `ro`.*,
                `o`.`ship_method_id`,
                `ui`.`company`,
                `ui`.`suite`,
                `ui`.`street_number`,
                `ui`.`street_name`,
                `ui`.`city`,
                `ui`.`zip`,
                `ui`.`address_type2`,
                `ui`.`first_name`,
                `ui`.`last_name`,
                `ui`.`phone_1`
            FROM `jos_vm_routes_orders` AS `ro` 
            INNER JOIN `jos_vm_order_user_info` AS `ui`
                ON
                `ui`.`order_id`=`ro`.`order_id`
                AND
                `ui`.`address_type`='ST'  
            LEFT JOIN `jos_vm_orders` AS `o`
                ON
                `o`.`order_id`=`ro`.`order_id`
            WHERE 
                `ro`.`route_id`=" . $route->id . "
            ORDER BY 
                `ro`.`queue`";
    $ordersSql = $mysqli->query($query);


    $route->orders = array();

    while ($order = $ordersSql->fetch_object()) {
        $order->address = 'Phone: ' . $order->phone_1 . "\nAddress: " . ((!empty($order->suite)) ? $order->suite . '#, ' : '') . $order->street_number . ' ' . $order->street_name . ", " . $order->city . ', ' . $order->zip . '';

        $route->orders[] = $order;
    }
    $map_orders = array();
    $headers = array('#', 'Order ID', 'Information', 'Full name (Company)', 'Distance/Duration');
    $totals = ['distance' => 0, 'duration' => 0];
    foreach ($route->orders as $order_obj) {
        $ship_details = explode("|", $order_obj->ship_method_id);
        $shipping_id = (int)$ship_details[4];
        if (!array_key_exists($shipping_id, $shipping_method_names)) {
            $shipping_id = (int)$ship_details[5];
        }

        $map_orders[] = array(
            $order_obj->queue,
            $order_obj->order_id . " \n " . $order_obj->address_type2 . " \n " . (($shipping_id == '25') ? ' (AM)' : ''),
            $order_obj->address,
            $order_obj->first_name . ' ' . $order_obj->last_name . ' ' . ((!empty($order_obj->company)) ? '(' . $order_obj->company . ')' : ''),
            $order_obj->distance . ' / ' . $order_obj->duration
        );
        $totals['distance'] += $order_obj->distance_raw;
        $totals['duration'] += $order_obj->duration_raw;
    }


    $pdf = new PDF_MC_Table();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 20);
    $pdf->Cell($pdf->GetPageWidth(), 0, 'Route #' . $route->route_id . ', ' . $route->warehouse_name . ', ' . $route->driver_name . ', ' . $route->route_date);
    if (!empty($route->map_image)) {
        $pdf->Image($route->map_image, 0, 20, $pdf->GetPageWidth(), $pdf->GetPageWidth() / 2, 'PNG');
        $pdf->SetY($pdf->GetPageWidth() / 2);
    }
    $pdf->SetFont('Arial', '', 9);
    //Route ID, Warehouse, Driver name, Route Date
    $pdf->SetX(0);
    $pdf->SetY($pdf->GetY() + 20);

    $pdf->SetWidths(array(5, 30, 80, 45, 30));

    $pdf->Row($headers);
    foreach ($map_orders as $order) {
        $pdf->Row($order);
    }
    $time_in_route = round($totals['duration']);
    $additional_time = count($map_orders) * ($mosConfig_driver_app_stop_time ?? 0);
    $warehouse_time = $mosConfig_driver_app_warehouse_time ?? 0;
    $total_time = $time_in_route + $additional_time + $warehouse_time;
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 8, 'Total distance: ' . round($totals['distance'] / 1000) . ' km', 0, 0, 'R');
    $pdf->Ln(1);
    $pdf->Cell(190, 16, 'Warehouse time: ' . sprintf('%02d:%02d', ($warehouse_time / 3600), ($warehouse_time / 60 % 60)), 0, 0, 'R');
    $pdf->Ln(1);
    $pdf->Cell(190, 24, 'Time in route: ' . sprintf('%02d:%02d', ($time_in_route / 3600), ($time_in_route / 60 % 60)), 0, 0, 'R');
    $pdf->Ln(1);
    $pdf->Cell(190, 32, 'Additional time between stops: ' . sprintf('%02d:%02d', ($additional_time / 3600), ($additional_time / 60 % 60)), 0, 0, 'R');
    $pdf->Ln(1);
    $pdf->Cell(190, 40, 'Total time: ' . sprintf('%02d:%02d', ($total_time / 3600), ($total_time / 60 % 60)), 0, 0, 'R');
    $pdf->Ln(1);
    $pdf->Cell(190, 48, 'Total orders: ' . count($map_orders), 0, 0, 'R');
    $pdf->Output();
    $mysqli->close();
}

