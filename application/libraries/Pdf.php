<?php

/**
 * rSanjoSEO
 *
 * Copyright (c) 2014 - 2017, Rafael San José Tovar
 *
 * @author      rSanjoSEO (rsanjose@alxarafe.com)
 * @copyright   Copyright (c)2017, Rafael San José Tovar (https://alxarafe.es/)
 * @license     Prohibida su distribución total o parcial. Uso sujeto a contrato comercial.
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf extends TCPDF
{
    protected $pagesize, $orientation, $fonttype, $fontsize;
    protected $leftmargin, $rightmargin, $topmargin;

    function __construct()
    {
        parent::__construct();

        $this->leftmargin = 20;
        $this->topmargin = 20;
        $this->rightmargin = -20;
    }

    protected function newPage()
    {
        $this->SetMargins($this->leftmargin, $this->topmargin, $this->rightmargin, false);
        $this->AddPage($this->orientation, $this->pagesize);
    }

    protected function GetSplitterStringWidth($text, $font = '', $style = '')
    {
        $ret = 0;
        $atext = explode("\n", $text);
        foreach ($atext as $text) {
            $len = $this->GetStringWidth($text, $font, $style);
            if ($len > $ret) {
                $ret = $len;
            }
        }
        return $ret;
    }
}

class PdfTableReport extends Pdf
{
    protected $struct, $fieldmargin, $breaks, $totals, $totaltypes, $indentation;
    protected $start_x, $start_y, $rowlines, $rowpoints, $maxwidth;

    function __construct()
    {
        parent::__construct();

        $this->fieldmargin = 4;
        $this->indentation = true;
        $this->start_x = $this->leftmargin;
    }

    protected function start($level)
    {
        if (!$this->indentation || ($level == 0)) {
            return $this->start_x;
        } else {
            return $this->start($level - 1) + $this->struct[$level - 1][key($this->struct[$level - 1])]['width'];
        }
    }

    // Hace que cada campo tenga el máximo número de líneas y actualiza
    // los atributos $this->rowlines y $this->rowpoints
    protected function sanitizeLine($level, $line)
    {
        $this->rowlines = 0;
        $this->rowpoints = 0;
        foreach ($line as $field => $value) {
            if (/*$this->breaks==null || */!is_numeric(array_search($field, $this->breaks))) {
                $res[$field] = trim($value);
                $nl = $this->getNumLines($value, $this->struct[$level][$field]['width']);
                if ($nl > $this->rowlines) {
                    $this->rowlines = $nl;
                }
                $px = $this->getStringHeight($this->struct[$level][$field]['width'], $value);
                if ($px > $this->rowpoints) {
                    $this->rowpoints = $px;
                }
            } else {
                $res[$field] = $value;
            }
        }

        // Forzamos a que todos los campos tengan la máxima longitud añadiendo retornos de carro a los que tengan menos líneas.
        foreach ($res as $field => $value) {
            if (/*$this->breaks==null || */!is_numeric(array_search($field, $this->breaks))) {
                while ($this->getNumLines($res[$field], $this->struct[$level][$field]['width']) < $this->rowlines) {
                    $res[$field] .= "\n";
                }
            }
        }

        return ($res);
    }

    protected function NewTablePage($level)
    {
        $this->AddPage($this->orientation, $this->pagesize);
        $this->printHeader($level);
    }

    protected function CloseTablePage($level)
    {
        $this->current_x = $this->start($level);
        $save_y = $this->current_y;
        $width = 0;
        foreach ($this->struct[$level] as $value) {
            $width += $value['width'];
        }
        $this->MultiCell($width, 0, '', 'T', 'J', false, 0, $this->current_x, $this->current_y);
        $this->current_y = $save_y;
    }

    protected function format($value, $type)
    {
        if ($type == 'T') {
            $m = floor($value / 60);
            $s = round(($value / 60 - $m) * 60, 2);
            $s = $s < 10 ? "0$s" : $s;
            $ret = "$m:$s";
        } elseif ($type == '€') {
            $ret = $value . '€';
        } elseif ($type == '$') {
            $ret = '$' . $value;
        } else {
            $ret = $value;
        }

        return $ret;
    }

    protected function Totalize($level)
    {
        $this->current_x = $this->start($level);

        if ($this->current_x > $this->start_x) {
            $this->MultiCell($this->current_x - $this->start_x, 0, '', 1, 'J', false, 0, $this->start_x, $this->current_y);
        }

        $res = '';
        $found = false;
        if (isset($this->counter)) {
            foreach ($this->struct[$level] as $field => $size) {
                $found = $found || (isset($this->counter[$level][$field]));
            }
            if ($found) {
                foreach ($this->struct[$level] as $field => $struct) {
                    if (isset($this->counter[$level][$field])) {
                        $res = $this->format($this->counter[$level][$field], $this->totaltypes[$field]);
                        $this->counter[$level][$field] = 0;
                    } else {
                        $res = '';
                    }
                    $this->MultiCell($struct['width'], 0, $res, 1, 'R', false, 0, $this->current_x, $this->current_y);
                    $this->current_x += $struct['width'];
                }
            } else {
                foreach ($this->counter[$level] as $fld => $suma) {
                    $res .= "Total {$fld} " . $this->format($suma, $this->totaltypes[$fld]) . ". ";
                    $this->counter[$level][$fld] = 0;
                }
            }
        }
        /*
        if (!$found) {
            $this->MultiCell(array_sum($this->sizes[$level]), 0, $res, 1, 'R', false, 0, $this->current_x, $this->current_y);
            $this->current_x+=array_sum($this->sizes[$level]);
        }

        if ($this->current_x<$this->maxwidth+$this->start_x) {
            //$this->MultiCell($this->maxwidth-$this->current_x, 0, '', 1, 'J', false, 0, $this->current_x, $this->current_y);
            $this->MultiCell($this->maxwidth+$this->start_x-$this->GetX(), 0, '', 1, 'L', 0, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
        }
        */

        if ($found) {
            if ($this->current_x < $this->maxwidth + $this->start_x) {
                //$this->MultiCell($this->maxwidth-$this->current_x, 0, '', 1, 'J', false, 0, $this->current_x, $this->current_y);
                $this->MultiCell($this->maxwidth + $this->start_x - $this->GetX(), 0, '', 1, 'L', 0, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
            }
        } else {
            if ($this->current_x < $this->maxwidth + $this->start_x) {
                //$this->MultiCell($this->maxwidth-$this->current_x, 0, '', 1, 'J', false, 0, $this->current_x, $this->current_y);
                $this->MultiCell($this->maxwidth + $this->start_x - $this->current_x, 0, $res, 1, 'R', 0, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
            }
        }


        $this->current_y += 5;
    }

    protected function preProcessArray($data, $level = 0)
    {
        foreach ($data as $row) {
            foreach ($row as $key => $field) {
                if (is_array($field)) {
                    //$_level=array_search($key, $this->breaks);
                    //$_level=isset($level)?$level+1:0;
                    $this->preProcessArray($field, $level + 1);
                } else {
                    if (!isset($this->struct[$level][$key]['type'])) {
                        $this->struct[$level][$key]['type'] = 'N';
                    }
                    if (!isset($this->struct[$level][$key]['width'])) {
                        $this->struct[$level][$key]['width'] = 0;
                    }

                    $l = $this->GetSplitterStringWidth($field) + $this->fieldmargin;
                    if ($l > $this->struct[$level][$key]['width']) {
                        $this->struct[$level][$key]['width'] = $l;
                    }

                    $this->struct[$level][$key]['type'] = ($this->struct[$level][$key]['type'] == 'N' && ((trim($field) == '') || is_numeric($field)));
                }
            }
        }
    }

    protected function processArray($data, $labels, $widths)
    {
        $this->preProcessArray($data, 0);

        $this->SetFont($this->fonttype, 'B', $this->fontsize);
        foreach ($this->struct as $level => $row) {
            $i = 0;
            foreach ($row as $key => $field) {
                // Asignamos la etiqueta de la columna (si no está definida usamos el nombre del campo)
                //$this->struct[$level][$key]['label']=isset($labels[$level][$i])?$labels[$level][$i]:(isset($labels[$i]) && is_string($labels[$i])?$labels[$i]:$key);
                $this->struct[$level][$key]['label'] = isset($labels[$level][$i]) ? $labels[$level][$i] : $key;
                // processArray nos ha puesto en width la mayor longitud de los datos, ahora comprobamos si label es mayor.
                $l = $this->GetSplitterStringWidth($this->struct[$level][$key]['label'], '', 'B') + $this->fieldmargin;
                if ($l > $this->struct[$level][$key]['width']) {
                    $this->struct[$level][$key]['width'] = $l;
                }
                $i++;
            }
        }
        $this->SetFont('');
        $this->maxwidth = 0;
        foreach ($this->struct as $level => $array) {
            $width = $this->start($level) - $this->start_x;
            foreach ($array as $value) {
                $width += $value['width'];
            }
            if ($this->maxwidth < $width) {
                $this->maxwidth = $width;
            }
        }
    }

    protected function printHeader($level)
    {
        //$this->Cell(0, 0, "Cabecera de nivel '$level'");
        //$this->Ln();
        $this->current_y = $this->GetY();

        // Ponemos el encabezado
        $this->current_x = $this->start($level);

        //$this->SetX($this->current_x);

        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');

        if ($this->GetY() >= $this->getPageHeight() - $this->getFooterMargin() - $this->rowpoints - 5) {
            $this->CloseTablePage($level);
            $this->NewTablePage($level);
        }

        /*

        if ($this->current_x>$this->start_x)
            $this->Cell($this->current_x-$this->start_x, 5, '', 1, 0, 'C', 1);

        foreach($this->struct[$level] as $key=>$value) {
            $this->Cell($value['width'], 5, $value['label'], 1, 0, 'C', 1);
        }

        if ($this->GetX()<$this->maxwidth+$this->start_x)
            $this->Cell($this->maxwidth+$this->start_x-$this->GetX(), 5, '', 1, 0, 'C', 1);
        */

        /* */

        // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

        $this->SetFillColor(255, 128, 128);

        if ($this->start_x < $this->current_x) {
            $this->MultiCell($this->current_x - $this->start_x, 0, '', 'LR', 'C', 1, $this->rowlines, $this->start_x, $this->current_y, true, 0, false, true, 0, 'T', true);
        }

        $this->SetFillColor(255, 0, 0);

        foreach ($this->struct[$level] as $key => $value) {
            $this->MultiCell($value['width'], 0, $value['label'], 'LR', 'C', 1, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
            $this->current_x += $value['width'];
        }

        $this->SetFillColor(255, 128, 128);


        if ($this->current_x < $this->maxwidth + $this->start_x) {
            $this->MultiCell($this->maxwidth - $this->current_x + $this->start_x, 0, '', 'LR', 'L', 1, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
        }
        /* */


        //$this->Ln();
        //$this->current_y=$this->GetY();
        $this->current_x = $this->start($level);
        $this->current_y += 4;

        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
    }

    // Un bloque (Block) es un trozo de tabla entre cabecera y subtotales.
    // Si dentro hay más bloques se hacen llamadas recursivas hasta que sólo queden líneas y se imprimen.
    protected function printBlock($block, $level = 0)
    {
        //echo "<h1>$level</h1>";
        // Imprimimos la cabecera del bloque con el nombre de los campos.
        $this->printHeader($level);
        $fill = 0;  // El formato pijama se empieza con blanco y alterna con gris en los siguientes registros.
        foreach ($block as $fld => $row) {
            //test_array("$fld",$row,false);
            $this->current_x = $this->start($level);
            $row = $this->sanitizeLine($level, $row);

            if ($this->current_y >= $this->getPageHeight() - $this->getFooterMargin() - $this->rowpoints - 5) {
                //die("Nueva página");
                $this->CloseTablePage($level);
                $this->NewTablePage($level);
                $fill = 0;
            }

            // Si hay que dejar un sangrado en la línea, se deja.
            if ($this->current_x > $this->start_x) {
                $this->MultiCell($this->current_x - $this->start_x, 0, '', 'LR', 'L', $fill, $this->rowlines, $this->start_x, $this->current_y, true, 0, false, true, 0, 'T', true);
            }

            // Recorremos los campos de la línea uno a uno
            foreach ($row as $field => $value) {
                // Si es un campo de ruptura, es que hablamos de un nuevo bloque
                if (is_numeric(array_search($field, $this->breaks))) {
                    // Vamos a hacer que la línea actual llegue hasta el final porque de aquí nos vamos...
                    if ($this->current_x < $this->maxwidth + $this->start_x) {
                        $this->MultiCell($this->maxwidth + $this->start_x - $this->current_x, 0, '', 'LR', 'L', $fill, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
                    }

                    $this->printBlock($value, $level + 1);
                    continue 2;
                } else {    // Imprimir el campo
                    if ($this->struct[$level][$field]['type'] == 'N') {
                        $res = ($value == '') ? '' : number_format(trim($value));
                        while ($this->getNumLines($res, $this->struct[$level][$field]['width']) < $this->rowlines) {
                            $res .= "\n";
                        }
                    } else {
                        $res = $value;
                    }

                    if (is_numeric(array_search($field, $this->totals))) {
                        for ($i = 0; $i <= $level; $i++) {
                            if (!isset($this->counter[$i][$field])) {
                                $this->counter[$i][$field] = 0;
                            }
                            $this->counter[$i][$field] += (float)trim($res);
                        }
                    }

                    $this->MultiCell($this->struct[$level][$field]['width'], 0, $res, 'LR', ($this->struct[$level][$field]['type'] == 'N') ? 'R' : 'L', $fill, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
                    $this->current_x += $this->struct[$level][$field]['width'];

                    //if ($level==0) echo "<p>$field con $this->current_x</p>";
                }
            }

            if ($this->current_x < $this->maxwidth/*+$this->start_x*/) {
                $this->MultiCell($this->maxwidth - $this->current_x, 0, 'X', 'LR', 'L', $fill, $this->rowlines, $this->current_x, $this->current_y, true, 0, false, true, 0, 'T', true);
            }

            $this->current_y += $this->rowpoints;
            $fill = $fill ? 0 : 1;
        }
        $this->CloseTablePage($level);
        $this->Totalize($level);
    }

    function printDocument($data, $format = array())
    {
        if (!is_array($data) || count($data) < 1) {
            return;
        }

        //test_array('Data',$data);

        $_names = isset($format['names']) ? $format['names'] : null;
        if ($_names == null || !is_array($_names[0])) {
            $names[0] = $_names;
        } else {
            $names = $_names;
        }
        $columns = isset($format['columns']) ? $format['columns'] : null;
        $this->totaltypes = isset($format['totals']) ? $format['totals'] : array();
        $this->totals = array();
        foreach ($this->totaltypes as $key => $value) {
            $this->totals[] = $key;
        }
        $this->breaks = isset($format['breaks']) ? $format['breaks'] : array();
        $this->orientation = isset($format['orientation']) ? $format['orientation'] : 'P';
        $this->pagesize = isset($format['pagesize']) ? $format['pagesize'] : 'A4';
        $this->fonttype = isset($format['font']) ? $format['font'] : 'helvetica';
        $this->fontsize = isset($format['fontsize']) ? $format['fontsize'] : 8;

        // Establecemos los parámetros de la cabecera del listado...
        $PDF_HEADER_TITLE = isset($format['title']) ? $format['title'] : 'Título (dejar en blanco en producción)';
        $PDF_HEADER_STRING = isset($format['subtitle']) ? $format['subtitle'] : 'Subtítulo (dejar en blanco en producción)';
        $PDF_HEADER_LOGO = '../../../../../img/logo.jpg';
        $PDF_HEADER_LOGO_WIDTH = 30;
        $PDF_HEADER_LOGO = null;

        $this->SetTitle($PDF_HEADER_TITLE);

        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont($this->fonttype, 'B', $this->fontsize);

        $this->setHeaderData(
            $PDF_HEADER_LOGO,
            PDF_HEADER_LOGO_WIDTH,
            $PDF_HEADER_TITLE,
            $PDF_HEADER_STRING
        );

        // Genera $this->struct con la estructura completa de la tabla
        $this->processArray($data, $names, $columns);

        // Crea la primera página e imprime los datos
        $this->newPage();
        $this->printBlock($data);
    }
}

class PdfReport extends PdfTableReport
{
    function __construct()
    {
        parent::__construct();

        /*
         * $this->SetHeaderMargin(0);
        $this->SetTopMargin(20);
        $this->setFooterMargin(20);
        */

        $this->leftmargin = 20;
        $this->topmargin = 20;
        $this->rightmargin = 20;
    }
}

class PdfDocument extends PdfTableReport
{
    function __construct()
    {
        parent::__construct();
        /*
        $this->SetHeaderMargin(0);
        $this->SetTopMargin(60);
        $this->setFooterMargin(20);
        */
        $this->leftmargin = 20;
        $this->topmargin = 20;
        $this->rightmargin = 20;
    }
}
