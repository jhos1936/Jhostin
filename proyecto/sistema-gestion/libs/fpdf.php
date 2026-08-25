<?php
/*******************************************************************************
* FPDF                                                                         *
* Version: 1.82                                                                *
*******************************************************************************/

define('FPDF_VERSION', '1.82');

class FPDF
{
protected $page;
protected $n;
protected $offsets;
protected $buffer;
protected $pages;
protected $state;
protected $compress;
protected $k;
protected $DefOrientation;
protected $CurOrientation;
protected $StdPageSizes;
protected $DefPageSize;
protected $CurPageSize;
protected $CurRotation;
protected $PageInfo;
protected $wPt, $hPt;
protected $w, $h;
protected $lMargin;
protected $tMargin;
protected $rMargin;
protected $bMargin;
protected $cMargin;
protected $x, $y;
protected $lasth;
protected $LineWidth;
protected $fontpath;
protected $CoreFonts;
protected $fonts;
protected $FontFiles;
protected $encodings;
protected $cmaps;
protected $FontFamily;
protected $FontStyle;
protected $underline;
protected $CurrentFont;
protected $FontSizePt;
protected $FontSize;
protected $DrawColor;
protected $FillColor;
protected $TextColor;
protected $ColorFlag;
protected $WithAlpha;
protected $ws;
protected $images;
protected $PageLinks;
protected $links;
protected $AutoPageBreak;
protected $PageBreakTrigger;
protected $InHeader;
protected $InFooter;
protected $AliasNbPages;
protected $ZoomMode;
protected $LayoutMode;
protected $metadata;
protected $PDFVersion;
protected $diffs;

function __construct($orientation='P', $unit='mm', $size='A4')
{
    $this->diffs = array();
    $this->state = 0;
    $this->page = 0;
    $this->n = 2;
    $this->buffer = '';
    $this->pages = array();
    $this->PageInfo = array();
    $this->fonts = array();
    $this->FontFiles = array();
    $this->encodings = array();
    $this->cmaps = array();
    $this->images = array();
    $this->links = array();
    $this->InHeader = false;
    $this->InFooter = false;
    $this->lasth = 0;
    $this->FontFamily = '';
    $this->FontStyle = '';
    $this->FontSizePt = 12;
    $this->underline = false;
    $this->DrawColor = '0 G';
    $this->FillColor = '0 g';
    $this->TextColor = '0 g';
    $this->ColorFlag = false;
    $this->WithAlpha = false;
    $this->ws = 0;
    
    if($unit=='pt') $this->k = 1;
    elseif($unit=='mm') $this->k = 72/25.4;
    elseif($unit=='cm') $this->k = 72/2.54;
    elseif($unit=='in') $this->k = 72;
    else $this->Error('Incorrect unit: '.$unit);
    
    $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
    
    $this->StdPageSizes = array(
        'a3'=>array(841.89,1190.55), 'a4'=>array(595.28,841.89), 'a5'=>array(420.94,595.28),
        'letter'=>array(612,792), 'legal'=>array(612,1008)
    );
    
    $size = $this->_getpagesize($size);
    $this->DefPageSize = $size;
    $this->CurPageSize = $size;
    
    $orientation = strtolower($orientation);
    if($orientation=='p' || $orientation=='portrait') {
        $this->DefOrientation = 'P';
        $this->w = $size[0];
        $this->h = $size[1];
    } elseif($orientation=='l' || $orientation=='landscape') {
        $this->DefOrientation = 'L';
        $this->w = $size[1];
        $this->h = $size[0];
    } else $this->Error('Incorrect orientation: '.$orientation);
    
    $this->CurOrientation = $this->DefOrientation;
    $this->wPt = $this->w * $this->k;
    $this->hPt = $this->h * $this->k;
    $this->CurRotation = 0;
    
    $margin = 28.35 / $this->k;
    $this->SetMargins($margin, $margin);
    $this->cMargin = $margin/10;
    $this->LineWidth = .567 / $this->k;
    $this->SetAutoPageBreak(true, 2*$margin);
    $this->SetDisplayMode('default');
    $this->SetCompression(true);
    $this->PDFVersion = '1.3';
}

function SetMargins($left, $top, $right=null)
{
    $this->lMargin = $left;
    $this->tMargin = $top;
    if($right===null) $right = $left;
    $this->rMargin = $right;
}

function SetLeftMargin($margin)
{
    $this->lMargin = $margin;
    if($this->page>0 && $this->x<$margin) $this->x = $margin;
}

function SetTopMargin($margin) { $this->tMargin = $margin; }
function SetRightMargin($margin) { $this->rMargin = $margin; }

function SetAutoPageBreak($auto, $margin=0)
{
    $this->AutoPageBreak = $auto;
    $this->bMargin = $margin;
    $this->PageBreakTrigger = $this->h-$margin;
}

function SetDisplayMode($zoom, $layout='default')
{
    if($zoom=='fullpage' || $zoom=='fullwidth' || $zoom=='real' || $zoom=='default' || !is_string($zoom))
        $this->ZoomMode = $zoom;
    else $this->Error('Incorrect zoom display mode: '.$zoom);
    if($layout=='single' || $layout=='continuous' || $layout=='two' || $layout=='default')
        $this->LayoutMode = $layout;
    else $this->Error('Incorrect layout display mode: '.$layout);
}

function SetCompression($compress)
{
    $this->compress = function_exists('gzcompress') ? $compress : false;
}

function SetTitle($title, $isUTF8=false) { $this->metadata['Title'] = $isUTF8 ? $title : utf8_encode($title); }
function SetAuthor($author, $isUTF8=false) { $this->metadata['Author'] = $isUTF8 ? $author : utf8_encode($author); }
function SetSubject($subject, $isUTF8=false) { $this->metadata['Subject'] = $isUTF8 ? $subject : utf8_encode($subject); }
function SetKeywords($keywords, $isUTF8=false) { $this->metadata['Keywords'] = $isUTF8 ? $keywords : utf8_encode($keywords); }
function SetCreator($creator, $isUTF8=false) { $this->metadata['Creator'] = $isUTF8 ? $creator : utf8_encode($creator); }
function AliasNbPages($alias='{nb}') { $this->AliasNbPages = $alias; }
function Error($msg) { throw new Exception('FPDF error: '.$msg); }

function Close()
{
    if($this->state==3) return;
    if($this->page==0) $this->AddPage();
    $this->InFooter = true;
    $this->Footer();
    $this->InFooter = false;
    $this->_endpage();
    $this->_enddoc();
}

function AddPage($orientation='', $size='', $rotation=0)
{
    if($this->state==0) $this->Open();
    $family = $this->FontFamily;
    $style = $this->FontStyle.($this->underline ? 'U' : '');
    $fontsize = $this->FontSizePt;
    $lw = $this->LineWidth;
    $dc = $this->DrawColor;
    $fc = $this->FillColor;
    $tc = $this->TextColor;
    $cf = $this->ColorFlag;
    if($this->page>0) {
        $this->InFooter = true;
        $this->Footer();
        $this->InFooter = false;
        $this->_endpage();
    }
    $this->_beginpage($orientation, $size, $rotation);
    $this->LineWidth = $lw;
    $this->_out(sprintf('%.2F w', $lw*$this->k));
    if($family) $this->SetFont($family, $style, $fontsize);
    $this->DrawColor = $dc;
    if($dc!='0 G') $this->_out($dc);
    $this->FillColor = $fc;
    if($fc!='0 g') $this->_out($fc);
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
    $this->InHeader = true;
    $this->Header();
    $this->InHeader = false;
    if($this->LineWidth!=$lw) {
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w', $lw*$this->k));
    }
    if($family) $this->SetFont($family, $style, $fontsize);
    if($this->DrawColor!=$dc) {
        $this->DrawColor = $dc;
        $this->_out($dc);
    }
    if($this->FillColor!=$fc) {
        $this->FillColor = $fc;
        $this->_out($fc);
    }
    $this->TextColor = $tc;
    $this->ColorFlag = $cf;
}

function Open() { $this->state = 1; }
function Header() {}
function Footer() {}
function PageNo() { return $this->page; }

function SetDrawColor($r, $g=null, $b=null)
{
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->DrawColor = sprintf('%.3F G', $r/255);
    else
        $this->DrawColor = sprintf('%.3F %.3F %.3F RG', $r/255, $g/255, $b/255);
    if($this->page>0) $this->_out($this->DrawColor);
}

function SetFillColor($r, $g=null, $b=null)
{
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->FillColor = sprintf('%.3F g', $r/255);
    else
        $this->FillColor = sprintf('%.3F %.3F %.3F rg', $r/255, $g/255, $b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
    if($this->page>0) $this->_out($this->FillColor);
}

function SetTextColor($r, $g=null, $b=null)
{
    if(($r==0 && $g==0 && $b==0) || $g===null)
        $this->TextColor = sprintf('%.3F g', $r/255);
    else
        $this->TextColor = sprintf('%.3F %.3F %.3F rg', $r/255, $g/255, $b/255);
    $this->ColorFlag = ($this->FillColor!=$this->TextColor);
}

function GetStringWidth($s)
{
    $s = (string)$s;
    $cw = &$this->CurrentFont['cw'];
    $w = 0;
    $l = strlen($s);
    for($i=0;$i<$l;$i++) $w += $cw[$s[$i]];
    return $w * $this->FontSize / 1000;
}

function SetLineWidth($width)
{
    $this->LineWidth = $width;
    if($this->page>0) $this->_out(sprintf('%.2F w', $width*$this->k));
}

function Line($x1, $y1, $x2, $y2)
{
    $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S', $x1*$this->k, ($this->h-$y1)*$this->k, $x2*$this->k, ($this->h-$y2)*$this->k));
}

function Rect($x, $y, $w, $h, $style='')
{
    if($style=='F') $op = 'f';
    elseif($style=='FD' || $style=='DF') $op = 'B';
    else $op = 'S';
    $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s', $x*$this->k, ($this->h-$y)*$this->k, $w*$this->k, -$h*$this->k, $op));
}

function AddFont($family, $style='', $file='')
{
    $family = strtolower($family);
    if($file=='') $file = str_replace(' ', '', $family).strtolower($style).'.php';
    $style = strtoupper($style);
    if($style=='IB') $style = 'BI';
    $fontkey = $family.$style;
    if(isset($this->fonts[$fontkey])) return;
    $info = $this->_loadfont($file);
    $info['i'] = count($this->fonts)+1;
    if(!empty($info['file'])) {
        if($info['type']=='TrueType')
            $this->FontFiles[$info['file']] = array('length1'=>$info['originalsize']);
        else
            $this->FontFiles[$info['file']] = array('length1'=>$info['size1'], 'length2'=>$info['size2']);
    }
    $this->fonts[$fontkey] = $info;
}

function SetFont($family, $style='', $size=0)
{
    if($family=='') $family = $this->FontFamily;
    else $family = strtolower($family);
    $style = strtoupper($style);
    if(strpos($style,'U')!==false) {
        $this->underline = true;
        $style = str_replace('U', '', $style);
    } else $this->underline = false;
    if($style=='IB') $style = 'BI';
    if($size==0) $size = $this->FontSizePt;
    if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size) return;
    $fontkey = $family.$style;
    if(!isset($this->fonts[$fontkey])) {
        if($family=='arial') $family = 'helvetica';
        if(in_array($family, $this->CoreFonts)) {
            if($family=='symbol' || $family=='zapfdingbats') $style = '';
            $fontkey = $family.$style;
            if(!isset($this->fonts[$fontkey])) $this->AddFont($family, $style);
        } else $this->Error('Undefined font: '.$family.' '.$style);
    }
    $this->FontFamily = $family;
    $this->FontStyle = $style;
    $this->FontSizePt = $size;
    $this->FontSize = $size / $this->k;
    $this->CurrentFont = &$this->fonts[$fontkey];
    if($this->page>0) $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

function SetFontSize($size)
{
    if($this->FontSizePt==$size) return;
    $this->FontSizePt = $size;
    $this->FontSize = $size / $this->k;
    if($this->page>0) $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
}

function AddLink() { $n = count($this->links)+1; $this->links[$n] = array(0,0); return $n; }
function SetLink($link, $y=0, $page=-1) { if($y==-1) $y=$this->y; if($page==-1) $page=$this->page; $this->links[$link]=array($page,$y); }
function Link($x,$y,$w,$h,$link) { $this->PageLinks[$this->page][]=array($x*$this->k,$this->hPt-$y*$this->k,$w*$this->k,$h*$this->k,$link); }

function Text($x, $y, $txt)
{
    if(!isset($this->CurrentFont)) $this->Error('No font has been set');
    $s = sprintf('BT %.2F %.2F Td (%s) Tj ET', $x*$this->k, ($this->h-$y)*$this->k, $this->_escape($txt));
    if($this->underline && $txt!='') $s .= ' '.$this->_dounderline($x, $y, $txt);
    if($this->ColorFlag) $s = 'q '.$this->TextColor.' '.$s.' Q';
    $this->_out($s);
}

function AcceptPageBreak() { return $this->AutoPageBreak; }

function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    $k = $this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()) {
        $x = $this->x;
        $ws = $this->ws;
        if($ws>0) { $this->ws=0; $this->_out('0 Tw'); }
        $this->AddPage($this->CurOrientation, $this->CurPageSize, $this->CurRotation);
        $this->x = $x;
        if($ws>0) { $this->ws=$ws; $this->_out(sprintf('%.3F Tw', $ws*$k)); }
    }
    if($w==0) $w = $this->w-$this->rMargin-$this->x;
    $s = '';
    if($fill || $border==1) {
        if($fill) $op = ($border==1) ? 'B' : 'f';
        else $op = 'S';
        $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x*$k, ($this->h-$this->y)*$k, $w*$k, -$h*$k, $op);
    }
    if(is_string($border)) {
        $x = $this->x;
        $y = $this->y;
        if(strpos($border,'L')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, $x*$k, ($this->h-($y+$h))*$k);
        if(strpos($border,'T')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-$y)*$k);
        if(strpos($border,'R')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x+$w)*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
        if(strpos($border,'B')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-($y+$h))*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
    }
    if($txt!=='') {
        if(!isset($this->CurrentFont)) $this->Error('No font has been set');
        if($align=='R') $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
        elseif($align=='C') $dx = ($w-$this->GetStringWidth($txt))/2;
        else $dx = $this->cMargin;
        if($this->ColorFlag) $s .= 'q '.$this->TextColor.' ';
        $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k, $this->_escape($txt));
        if($this->underline) $s .= ' '.$this->_dounderline($this->x+$dx, $this->y+.5*$h+.3*$this->FontSize, $txt);
        if($this->ColorFlag) $s .= ' Q';
        if($link) $this->Link($this->x+$dx, $this->y+.5*$h-.5*$this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
    }
    if($s) $this->_out($s);
    $this->lasth = $h;
    if($ln>0) {
        $this->y += $h;
        if($ln==1) $this->x = $this->lMargin;
    } else $this->x += $w;
}

function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=false)
{
    if(!isset($this->CurrentFont)) $this->Error('No font has been set');
    $cw = &$this->CurrentFont['cw'];
    if($w==0) $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n") $nb--;
    $b = 0;
    if($border) {
        if($border==1) {
            $border = 'LTRB';
            $b = 'LRT';
            $b2 = 'LR';
        } else {
            $b2 = '';
            if(strpos($border,'L')!==false) $b2 .= 'L';
            if(strpos($border,'R')!==false) $b2 .= 'R';
            $b = (strpos($border,'T')!==false) ? $b2.'T' : $b2;
        }
    }
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $ns = 0;
    $nl = 1;
    while($i<$nb) {
        $c = $s[$i];
        if($c=="\n") {
            if($this->ws>0) { $this->ws=0; $this->_out('0 Tw'); }
            $this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2) $b = $b2;
            continue;
        }
        if($c==' ') { $sep = $i; $ls = $l; $ns++; }
        $l += $cw[$c];
        if($l>$wmax) {
            if($sep==-1) {
                if($i==$j) $i++;
                if($this->ws>0) { $this->ws=0; $this->_out('0 Tw'); }
                $this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
            } else {
                if($align=='J') {
                    $this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                    $this->_out(sprintf('%.3F Tw', $this->ws*$this->k));
                }
                $this->Cell($w, $h, substr($s, $j, $sep-$j), $b, 2, $align, $fill);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            $nl++;
            if($border && $nl==2) $b = $b2;
        } else $i++;
    }
    if($this->ws>0) { $this->ws=0; $this->_out('0 Tw'); }
    if($border && strpos($border,'B')!==false) $b .= 'B';
    $this->Cell($w, $h, substr($s, $j, $i-$j), $b, 2, $align, $fill);
    $this->x = $this->lMargin;
}

function Write($h, $txt, $link='')
{
    if(!isset($this->CurrentFont)) $this->Error('No font has been set');
    $cw = &$this->CurrentFont['cw'];
    $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nb = strlen($s);
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while($i<$nb) {
        $c = $s[$i];
        if($c=="\n") {
            $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1) {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
            continue;
        }
        if($c==' ') $sep = $i;
        $l += $cw[$c];
        if($l>$wmax) {
            if($sep==-1) {
                if($this->x>$this->lMargin) {
                    $this->x = $this->lMargin;
                    $this->y += $h;
                    $w = $this->w-$this->rMargin-$this->x;
                    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
                    $i++;
                    $nl++;
                    continue;
                }
                if($i==$j) $i++;
                $this->Cell($w, $h, substr($s, $j, $i-$j), 0, 2, '', 0, $link);
            } else {
                $this->Cell($w, $h, substr($s, $j, $sep-$j), 0, 2, '', 0, $link);
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            if($nl==1) {
                $this->x = $this->lMargin;
                $w = $this->w-$this->rMargin-$this->x;
                $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
            }
            $nl++;
        } else $i++;
    }
    if($i!=$j) $this->Cell($l/1000*$this->FontSize, $h, substr($s, $j), 0, 0, '', 0, $link);
}

function Image($file, $x=null, $y=null, $w=0, $h=0, $type='', $link='')
{
    if($file=='') $this->Error('Image file name is empty');
    if(!isset($this->images[$file])) {
        if($type=='') {
            $pos = strrpos($file, '.');
            if(!$pos) $this->Error('Image file has no extension and no type was specified: '.$file);
            $type = substr($file, $pos+1);
        }
        $type = strtolower($type);
        if($type=='jpeg') $type = 'jpg';
        $mtd = '_parse'.$type;
        if(!method_exists($this, $mtd)) $this->Error('Unsupported image type: '.$type);
        $info = $this->$mtd($file);
        $info['i'] = count($this->images)+1;
        $this->images[$file] = $info;
    } else $info = $this->images[$file];
    if($w==0 && $h==0) { $w = -96; $h = -96; }
    if($w<0) $w = -$info['w'] * 72 / $w / $this->k;
    if($h<0) $h = -$info['h'] * 72 / $h / $this->k;
    if($w==0) $w = $h * $info['w'] / $info['h'];
    if($h==0) $h = $w * $info['h'] / $info['w'];
    if($x===null) $x = $this->x;
    if($y===null) $y = $this->y;
    $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q', $w*$this->k, $h*$this->k, $x*$this->k, ($this->h-($y+$h))*$this->k, $info['i']));
    if($link) $this->Link($x, $y, $w, $h, $link);
}

function GetX() { return $this->x; }
function SetX($x) { if($x>=0) $this->x=$x; else $this->x=$this->w+$x; }
function GetY() { return $this->y; }
function SetY($y, $resetX=true) { if($y>=0) $this->y=$y; else $this->y=$this->h+$y; if($resetX) $this->x=$this->lMargin; }
function SetXY($x, $y) { $this->SetX($x); $this->SetY($y, false); }

function Output($dest='', $name='', $isUTF8=false)
{
    $this->Close();
    if(strlen($name)==1 && strlen($dest)!=1) { $tmp=$dest; $dest=$name; $name=$tmp; }
    if($dest=='') $dest='I';
    if($name=='') $name='doc.pdf';
    switch(strtoupper($dest)) {
        case 'I':
            $this->_checkoutput();
            if(PHP_SAPI!='cli') {
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; '.$this->_httpencode('filename', $name, $isUTF8));
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
            }
            echo $this->buffer;
            break;
        case 'D':
            $this->_checkoutput();
            header('Content-Type: application/x-download');
            header('Content-Disposition: attachment; '.$this->_httpencode('filename', $name, $isUTF8));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            echo $this->buffer;
            break;
        case 'F': file_put_contents($name, $this->buffer); break;
        case 'S': return $this->buffer;
        default: $this->Error('Incorrect output destination: '.$dest);
    }
    return '';
}

function _checkoutput()
{
    if(PHP_SAPI!='cli') {
        if(headers_sent($file, $line)) $this->Error("Some data has already been output, can't send PDF file (output started at $file:$line)");
    }
    if(ob_get_length()) {
        if(ob_get_level() > 1) ob_end_clean();
        else $this->Error("Some data has already been output, can't send PDF file");
    }
}

function _getpagesize($size)
{
    if(is_string($size)) {
        $size = strtolower($size);
        if(!isset($this->StdPageSizes[$size])) $this->Error('Unknown page size: '.$size);
        $a = $this->StdPageSizes[$size];
        return array($a[0]/$this->k, $a[1]/$this->k);
    } else {
        if($size[0]>$size[1]) return array($size[1], $size[0]);
        else return $size;
    }
}

function _beginpage($orientation, $size, $rotation)
{
    $this->page++;
    $this->pages[$this->page] = '';
    $this->PageLinks[$this->page] = array();
    $this->state = 2;
    $this->x = $this->lMargin;
    $this->y = $this->tMargin;
    $this->FontFamily = '';
    if($orientation=='') $orientation = $this->DefOrientation;
    else $orientation = strtoupper($orientation[0]);
    if($size=='') $size = $this->DefPageSize;
    else $size = $this->_getpagesize($size);
    if($orientation!=$this->CurOrientation || $size[0]!=$this->CurPageSize[0] || $size[1]!=$this->CurPageSize[1]) {
        if($orientation=='P') { $this->w = $size[0]; $this->h = $size[1]; }
        else { $this->w = $size[1]; $this->h = $size[0]; }
        $this->wPt = $this->w * $this->k;
        $this->hPt = $this->h * $this->k;
        $this->PageBreakTrigger = $this->h - $this->bMargin;
        $this->CurOrientation = $orientation;
        $this->CurPageSize = $size;
    }
    if($orientation!=$this->DefOrientation || $size[0]!=$this->DefPageSize[0] || $size[1]!=$this->DefPageSize[1])
        $this->PageInfo[$this->page]['size'] = array($this->wPt, $this->hPt);
    if($rotation!=0) {
        if($rotation%90!=0) $this->Error('Incorrect rotation value: '.$rotation);
        $this->CurRotation = $rotation;
        $this->PageInfo[$this->page]['rotation'] = $rotation;
    }
}

function _endpage() { $this->state = 1; }

function _loadfont($font)
{
    include($this->fontpath.$font);
    if(!isset($name)) $this->Error('Could not include font definition file');
    if(isset($type)) {
        $info['type'] = $type;
        $info['name'] = $name;
        if(isset($desc)) $info['desc'] = $desc;
        if(isset($up)) $info['up'] = $up;
        if(isset($ut)) $info['ut'] = $ut;
        if(isset($cw)) $info['cw'] = $cw;
        if(isset($enc)) $info['enc'] = $enc;
        if(isset($file)) $info['file'] = $file;
        if(isset($size1)) $info['size1'] = $size1;
        if(isset($size2)) $info['size2'] = $size2;
        if(isset($originalsize)) $info['originalsize'] = $originalsize;
    } else $this->Error('Could not extract font information');
    return $info;
}

function _escape($s) { $s = str_replace('\\','\\\\',$s); $s = str_replace('(','\\(',$s); $s = str_replace(')','\\)',$s); $s = str_replace("\r",'\\r',$s); return $s; }
function _textstring($s) { return '(' . $this->_escape($s) . ')'; }

function _dounderline($x, $y, $txt)
{
    $up = $this->CurrentFont['up'];
    $ut = $this->CurrentFont['ut'];
    $w = $this->GetStringWidth($txt) + $this->ws * substr_count($txt, ' ');
    return sprintf('%.2F %.2F %.2F %.2F re f', $x*$this->k, ($this->h-($y-$up/1000*$this->FontSize))*$this->k, $w*$this->k, -$ut/1000*$this->FontSizePt);
}

function _parsejpg($file)
{
    $a = getimagesize($file);
    if(!$a) $this->Error('Missing or incorrect image file: '.$file);
    if($a[2]!=2) $this->Error('Not a JPEG file: '.$file);
    if(!isset($a['channels']) || $a['channels']==3) $colspace = 'DeviceRGB';
    elseif($a['channels']==4) $colspace = 'DeviceCMYK';
    else $colspace = 'DeviceGray';
    $bpc = isset($a['bits']) ? $a['bits'] : 8;
    $data = file_get_contents($file);
    return array('w'=>$a[0], 'h'=>$a[1], 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'DCTDecode', 'data'=>$data);
}

function _parsepng($file)
{
    $f = fopen($file, 'rb');
    if(!$f) $this->Error('Can\'t open image file: '.$file);
    $info = $this->_parsepngstream($f, $file);
    fclose($f);
    return $info;
}

function _parsepngstream($f, $file)
{
    if($this->_readstream($f, 8) != chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) $this->Error('Not a PNG file: '.$file);
    $this->_readstream($f, 4);
    if($this->_readstream($f, 4) != 'IHDR') $this->Error('Incorrect PNG file: '.$file);
    $w = $this->_readint($f);
    $h = $this->_readint($f);
    $bpc = ord($this->_readstream($f, 1));
    if($bpc>8) $this->Error('16-bit depth not supported: '.$file);
    $col = ord($this->_readstream($f, 1));
    switch($col) {
        case 0: $cs = 'DeviceGray'; break;
        case 2: $cs = 'DeviceRGB'; break;
        case 3: $cs = 'Indexed'; break;
        default: $this->Error('Alpha channel not supported: '.$file);
    }
    if(ord($this->_readstream($f, 1)) != 0) $this->Error('Unknown compression method: '.$file);
    if(ord($this->_readstream($f, 1)) != 0) $this->Error('Unknown filter method: '.$file);
    if(ord($this->_readstream($f, 1)) != 0) $this->Error('Interlacing not supported: '.$file);
    $this->_readstream($f, 4);
    $dp = '/Predictor 15 /Colors '.($col==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w;
    $pal = ''; $trns = ''; $data = '';
    do {
        $n = $this->_readint($f);
        $type = $this->_readstream($f, 4);
        if($type == 'PLTE') { $pal = $this->_readstream($f, $n); $this->_readstream($f, 4); }
        elseif($type == 'tRNS') {
            $t = $this->_readstream($f, $n);
            if($col == 3) $trns = $t;
            else { if(ord($t[0]) != 0 || ord($t[1]) != 0) $this->Error('Transparency alpha channel not supported'); }
            $this->_readstream($f, 4);
        }
        elseif($type == 'IDAT') { $data .= $this->_readstream($f, $n); $this->_readstream($f, 4); }
        elseif($type == 'IEND') break;
        else $this->_readstream($f, $n+4);
    } while($n);
    if($col==3 && !empty($pal)) $this->Error('Indexed PNG not supported');
    return array('w'=>$w, 'h'=>$h, 'cs'=>$cs, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'dp'=>$dp, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
}

function _readint($f) { $a = unpack('Ni', $this->_readstream($f, 4)); return $a['i']; }
function _readstream($f, $n) { $res=''; while($n>0 && !feof($f)) { $s=fread($f,$n); if($s===false) $this->Error('Error while reading stream'); $n-=strlen($s); $res.=$s; } if($n>0) $this->Error('Unexpected end of stream'); return $res; }
function _out($s) { if($this->state==2) $this->pages[$this->page] .= $s."\n"; elseif($this->state==1) $this->_put($s); elseif($this->state==0) $this->Error('No document open'); elseif($this->state==3) $this->Error('Document closed'); }
function _put($s) { $this->buffer .= $s."\n"; }
function _putheader() { $this->_put('%PDF-'.$this->PDFVersion); }

function _putpages()
{
    $nb = $this->page;
    $n = $this->n;
    for($i=1;$i<=$nb;$i++) {
        $this->_newobj();
        $this->_put('<</Type /Page');
        $this->_put('/Parent 1 0 R');
        if(isset($this->PageInfo[$i]['size'])) $this->_put(sprintf('/MediaBox [0 0 %.2F %.2F]', $this->PageInfo[$i]['size'][0], $this->PageInfo[$i]['size'][1]));
        if(isset($this->PageInfo[$i]['rotation'])) $this->_put('/Rotate '.$this->PageInfo[$i]['rotation']);
        $this->_put('/Resources 2 0 R');
        if(isset($this->PageLinks[$i])) {
            $annots = '/Annots [';
            foreach($this->PageLinks[$i] as $pl) {
                $rect = sprintf('%.2F %.2F %.2F %.2F', $pl[0], $pl[1], $pl[0]+$pl[2], $pl[1]-$pl[3]);
                $annots .= '<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
                if(is_string($pl[4])) $annots .= '/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
                else {
                    $l = $this->links[$pl[4]];
                    if(isset($this->PageInfo[$l[0]]['size'])) $h = $this->PageInfo[$l[0]]['size'][1];
                    else $h = ($this->DefOrientation=='P') ? $this->DefPageSize[1]*$this->k : $this->DefPageSize[0]*$this->k;
                    $annots .= sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]>>', 1+2*$l[0], $h-$l[1]*$this->k);
                }
            }
            $this->_put($annots.']');
        }
        $this->_put('/Contents '.($this->n+1).' 0 R>>');
        $this->_put('endobj');
        $this->_putpage($i);
    }
    $this->n = $n;
}

function _putpage($p)
{
    $this->_newobj();
    $this->_put('<</Length '.strlen($this->pages[$p]));
    if($this->compress) $this->_put('/Filter /FlateDecode');
    $this->_put('>>');
    $this->_put('stream');
    $this->_put($this->compress ? gzcompress($this->pages[$p]) : $this->pages[$p]);
    $this->_put('endstream');
    $this->_put('endobj');
}

function _putfonts()
{
    $nf = $this->n;
    foreach($this->diffs as $diff) {
        $this->_newobj();
        $this->_put('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
        $this->_put('endobj');
    }
    foreach($this->FontFiles as $file=>$info) {
        $this->_newobj();
        $this->_put('<</Type /FontFile');
        if($info['type']=='Type1') $this->_put('/Subtype /Type1');
        $this->_put('/Length1 '.$info['length1']);
        if(isset($info['length2'])) $this->_put('/Length2 '.$info['length2']);
        if(isset($info['length3'])) $this->_put('/Length3 '.$info['length3']);
        $this->_put('/Filter /FlateDecode');
        $this->_put('>>');
        $this->_put('stream');
        $this->_put(file_get_contents($this->fontpath.$file));
        $this->_put('endstream');
        $this->_put('endobj');
    }
    foreach($this->fonts as $k=>$font) {
        $this->_newobj();
        $this->_put('<</Type /Font');
        $this->_put('/BaseFont /'.$font['name']);
        if(isset($font['desc'])) $this->_put('/FontDescriptor '.($this->n+1).' 0 R');
        if(isset($font['enc'])) {
            if(isset($font['diff'])) $this->_put('/Encoding '.($nf+$font['diff']).' 0 R');
            else $this->_put('/Encoding /WinAnsiEncoding');
        }
        $this->_put('/FirstChar 32');
        $this->_put('/LastChar 255');
        $this->_put('/Widths '.($this->n+1).' 0 R');
        $this->_put('/Subtype /'.$font['type']);
        $this->_put('>>');
        $this->_put('endobj');
        $this->_fontdesc($font);
        $this->_putwidths($font);
    }
}

function _fontdesc($font)
{
    $this->_newobj();
    $this->_put('<</Type /FontDescriptor');
    $this->_put('/FontName /'.$font['name']);
    if(isset($font['desc']['Ascent'])) $this->_put('/Ascent '.$font['desc']['Ascent']);
    if(isset($font['desc']['Descent'])) $this->_put('/Descent '.$font['desc']['Descent']);
    if(isset($font['desc']['CapHeight'])) $this->_put('/CapHeight '.$font['desc']['CapHeight']);
    if(isset($font['desc']['Flags'])) $this->_put('/Flags '.$font['desc']['Flags']);
    if(isset($font['desc']['FontBBox'])) $this->_put('/FontBBox ['.sprintf('%.1F %.1F %.1F %.1F', $font['desc']['FontBBox'][0], $font['desc']['FontBBox'][1], $font['desc']['FontBBox'][2], $font['desc']['FontBBox'][3]).']');
    if(isset($font['desc']['ItalicAngle'])) $this->_put('/ItalicAngle '.$font['desc']['ItalicAngle']);
    if(isset($font['desc']['StemV'])) $this->_put('/StemV '.$font['desc']['StemV']);
    if(isset($font['desc']['MissingWidth'])) $this->_put('/MissingWidth '.$font['desc']['MissingWidth']);
    $this->_put('>>');
    $this->_put('endobj');
}

function _putwidths($font)
{
    $this->_newobj();
    $cw = &$font['cw'];
    $s = '[';
    for($i=32;$i<=255;$i++)
        $s .= $cw[chr($i)].' ';
    $this->_put($s.']');
    $this->_put('endobj');
}

function _putimages()
{
    foreach($this->images as $file=>$info) {
        $this->_newobj();
        $this->_put('<</Type /XObject');
        $this->_put('/Subtype /Image');
        $this->_put('/Width '.$info['w']);
        $this->_put('/Height '.$info['h']);
        if($info['cs']=='Indexed') $this->_put('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
        else {
            $this->_put('/ColorSpace /'.$info['cs']);
            if($info['cs']=='DeviceCMYK') $this->_put('/Decode [1 0 1 0 1 0 1 0]');
        }
        $this->_put('/BitsPerComponent '.$info['bpc']);
        if(isset($info['f'])) $this->_put('/Filter /'.$info['f']);
        if(isset($info['dp'])) $this->_put('/DecodeParms <<'.$info['dp'].'>>');
        if(isset($info['trns']) && is_string($info['trns'])) $this->_put('/Mask ['.$info['trns'].']');
        $this->_put('/Length '.strlen($info['data']));
        $this->_put('>>');
        $this->_put('stream');
        $this->_put($info['data']);
        $this->_put('endstream');
        $this->_put('endobj');
        if($info['cs']=='Indexed') {
            $this->_newobj();
            $this->_put($this->_textstring($info['pal']));
            $this->_put('endobj');
        }
    }
}

function _putresources() { $this->_putfonts(); $this->_putimages(); }
function _putinfo() { $this->_put('<<'); foreach($this->metadata as $key=>$value) $this->_put('/'.$key.' '.$this->_textstring($value)); $this->_put('/Producer '.$this->_textstring('FPDF '.FPDF_VERSION)); $this->_put('/CreationDate '.$this->_textstring('D:'.date('YmdHis'))); $this->_put('>>'); }
function _putcatalog() { $this->_put('<<'); $this->_put('/Type /Catalog'); $this->_put('/Pages 1 0 R'); if($this->ZoomMode!='default') { if(!is_string($this->ZoomMode)) $this->_put('/OpenAction [3 0 R /FitZ '.$this->ZoomMode.']'); else $this->_put('/OpenAction [3 0 R /'.$this->ZoomMode.']'); } if($this->LayoutMode!='default') $this->_put('/PageLayout /'.$this->LayoutMode); $this->_put('>>'); }
function _puttrailer() { $this->_put('<<'); $this->_put('/Size '.($this->n+1)); $this->_put('/Root '.$this->n.' 0 R'); $this->_put('/Info '.($this->n-1).' 0 R'); $this->_put('>>'); $this->_put('startxref'); $this->_put($this->offsets[0]); $this->_put('%%EOF'); }
function _newobj() { $this->n++; $this->offsets[$this->n] = strlen($this->buffer); $this->_put($this->n.' 0 obj'); }
function _enddoc() { $this->_putheader(); $this->_putpages(); $this->_putresources(); $this->_putinfo(); $this->_newobj(); $this->_putcatalog(); $this->_put('endobj'); $this->_newobj(); $this->_puttrailer(); $this->state = 3; }
function _dochecks() { if(1.1==1) return; throw new Exception('Don\'t alter the locale before including class file'); }
function _httpencode($param, $value, $isUTF8) { if($isUTF8) $value = utf8_encode($value); if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')!==false) return $param.'="'.$value.'"'; else return $param."*=UTF-8''".rawurlencode($value); }
}
?>