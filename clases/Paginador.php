<?php

class Paginador {

    private $registros;
    private $registrosxpagina;
    private $ver_pagina;

    public function __construct($array = '', $registrosxpagina = '') {
        if ($array != '') {
            $this->registros = $array;
            $this->registrosxpagina = $registrosxpagina;
        }
    }

    public function getPaginas() {
        $count = ceil(count($this->registros) / $this->registrosxpagina);
        return $count;
    }

    public function getRegistros() {
        return $this->registros;
    }

    public function setRegistros($registros) {
        $this->registros = $registros;
    }

    public function getCantRegistros() {
        return count($this->registros);
    }

    public function getPagina($nrodepagina = 1) {
        if (intval($nrodepagina)) {
            $this->ver_pagina = $nrodepagina;
        } else {
            if ($nrodepagina == '&gt;&gt;') {
                $this->ver_pagina = $this->getPaginas();
            } elseif ($nrodepagina == '&lt;&lt;') {
                $this->ver_pagina = 1;
            }
        }
        $offset = ($nrodepagina - 1) * $this->registrosxpagina;
        $pagina = array_slice($this->registros, $offset, $this->registrosxpagina);
        return $pagina;
    }

    public function getHTML() {
        if ($this->getCantRegistros()) {
            $html = "<div id='paginador' Style='float: left;display:block;width: 100%;'>
        <font id='total' style='float: left;font-size: small;color: #006699;margin-left: 10px'>Total:" . $this->getCantRegistros() . "</font>
        <div id='divpaginas' style='float: right;margin-right: 10px'>
            <font style='float: left;font-size: small;color: #006699;'>Paginas:</font>
            <ul id='paginas' style='display: inline;margin: 0'>";

//            if ($this->ver_pagina == $this->getPaginas()) {
//                $nro_pagina = $this->ver_pagina - 3;
//            } else {
//                
//            }
            $html.=" <li style='float: left;font-size: small;color: #006699;margin-left: 5px;";

            if ($this->ver_pagina == 1)
                $html.= "font-weight: bold;cursor: default;";
            else
                $html.= "cursor: pointer;";
            $html.= "'><<";
            $html.= "</li>";

            while ($nro_pagina <= $this->getPaginas()) {

                $html.=" <li style='float: left;font-size: small;color: #006699;margin-left: 5px;";

                if ($nro_pagina == $this->ver_pagina)
                    $html.= "font-weight: bold;cursor: default;";
                else
                    $html.= "cursor: pointer;";
                $html.= "'>";
                $html.= $nro_pagina;
                $html.= "</li>";

                $nro_pagina++;
            }

            $html.=" <li style='float: left;font-size: small;color: #006699;margin-left: 5px;";

            if ($this->getPaginas() == $this->ver_pagina)
                $html.= "font-weight: bold;cursor: default;";
            else
                $html.= "cursor: pointer;";
            $html.= "'>>>";
            $html.= "</li>";

            $html.= "</ul></div></div>";
        }else {
            $html = "<div id='paginador' Style='float: left;display:block;width: 100%;'>
        <font id='total' style='float: left;font-size: small;color: #006699;margin-left: 10px'>Total:" . $this->getCantRegistros() . "</font>
        <div id='divpaginas' style='float: right;margin-right: 10px'>
            <font style='float: left;font-size: small;color: #006699;'>Paginas: </font><ul id='paginas' style='display: inline;margin: 0'><il  style='float: left;font-size: small;color: #006699;margin-left: 5px;font-weight: bold;cursor: default;'>0</il></ul></div></div>";
        }

        return $html;
    }

}

?>
