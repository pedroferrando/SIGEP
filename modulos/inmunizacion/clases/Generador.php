<?php
require_once ("../../config.php"); 

class Generador {
   
    private $_result;
    private $_head;
    private $_query;
    private $_head_m;
    private $_icons;
    private $_icons_titles="N";
    private $_variables;
    private $_index="N";
    private $_title;
    
    private $_total_rows; //Cantidad de registros de la consulta
    private $_rowPerPage=1; //Cantidad de lineas por pagina. Por defecto 10
    private $_pageNum=1; //Pagina a mostrar. Por defecto pagina 1
    private $_offset; // Comienzo (desplazamiento)
    private $_total_page=0; // Cantidad de paginas calculadas. Por defecto 0
    
    function Generador($query,$rowPerPage){
        
        $result=  sql($query);
        //Se obtiene la cantidad total de lineas
        $this->_total_rows=$result->recordcount();
        
        $this->_rowPerPage=$rowPerPage;
        
        //Si cantidad de filas es mayor a cantidad de filas por pagina
        if($this->_total_rows > $this->_rowPerPage){
            //Calculando offset
            $this->_offset=($this->_pageNum - 1)* $this->_rowPerPage;
        
            //Calculo cantidad de paginas
            $this->_total_page=  ceil($this->_total_rows/$this->_rowPerPage);  
            
            $query=$query." limit $this->_rowPerPage offset $this->_offset";
            $result=  sql($query);
        }
        
        $this->_result=$result;
        $this->_query=$query;
        
        
        
        //Se obtienen los nombres de columnas 
        if($result){
                    $keys = (array_keys($result->fields));
                    if (!$result->EOF) {
                        $temp = array();
                        for ($i=1; $i < count($keys); $i+=2) { 
                                    $temp[] = $keys[$i];
                        }
                    }
        $this->_head=$temp; 
  
        }
        
    }   
    public function getResult(){
        return $this->_result;
    }
    public function getHeader(){
        return $this->_head;
    }
    public function getQuery(){
        return $this->_query;
    }
    public function getHtmlTHead(){

        $header=array();
        if($this->_index=="S"){
                $html="<th>#</th>";
                $header[]= $html;    
            }
        if($this->_head_m){
            foreach ($this->_head_m as $columna){
                $html="<th><span class='hlink' onclick='evt_click(\"hlink\")'>".$columna."</span></span></th>";
                $header[]= $html;    
            }
        }else{
            foreach ($this->_head as $columna){
                $html="<th><span class='hlink' onclick='evt_click(\"hlink\")'>".$columna."</span></span></th>";
                $header[]= $html;    
            }
        }
        if($this->_icons){                      
            foreach ($this->_icons as $icons){
                if($this->_icons_titles=="S"){
                    $html="<th>".$icons."</th>";
                }else{
                    $html="<th>    </th>";
                }
                $header[]= $html;    
            }
        }
        
        return $header;
    }
    public function getHtmlIndex(){
	 if ($this->_total_page > 1) {
                        echo '<div class="pagination">';
                        echo '<ul>';
                            if ($this->_pageNum != 1)
                                    echo '<li><a class="paginate" data="'.($this->_pageNum-1).'">Anterior</a></li>';
                            	for ($i=1;$i<=$this->_total_page;$i++) {
                                    if ($this->_pageNum == $i)
                                            //si muestro el índice de la página actual, no coloco enlace
                                            echo '<li class="active"><a>'.$i.'</a></li>';
                                    else
                                            //si el índice no corresponde con la página mostrada actualmente,
                                            //coloco el enlace para ir a esa página
                                            echo '<li><a class="paginate" data="'.$i.'">'.$i.'</a></li>';
                            }
                            if ($this->_pageNum != $this->_total_page)
                                    echo '<li><a class="paginate" data="'.($this->_pageNum+1).'">Siguiente</a></li>';
                       echo '</ul>';
                       echo '</div>';
          }
    }
    public function setTHead($head_m){
        $this->_head_m=$head_m;
    }
    public function setIcons($icons){
        $this->_icons=$icons;
    }
    public function setVariables($variables){
        $this->_variables=$variables;
    }
    public function setIndex($index){
        $this->_index=$index;
    }
    public function setRowPerPage($rowPerPage){
        $this->_rowPerPage=$rowPerPage;
    }
    public function getHtmlTBody(){
        $j=0;
        $count=  $this->_offset+1;
        $body=array();
        foreach ($this->_result as $fila){
            $html="";
            for ($i=0; $i < count($this->_head); $i++) { 
                $html=$html."<td>".$fila[$i]."</td>";
            }
            if($this->_icons){
                foreach($this->_icons as $icons){
                    if($icons=='trash'){
                       $html=$html."<td><span class='".$icons."' onclick='evt_click(\"$icons\")'><span class='lid'></span><span class='can'></span></span></td>";
                    }
                    else{
                       $html=$html."<td><span class='".$icons."' onclick='evt_click(\"$icons\")'></span></td>"; 
                    }
                }
                
            }
            if($this->_index=="S"){
                $html="<td>".$count."</td>".$html;
                $count++;
            }
            if($j==1){
                $body[]= "<tr class='con'>".$html."</tr>";  
                $j=0;
            } else{
                $body[]= "<tr class='sin'>".$html."</tr>";  
                $j=1;
            }
        }
        return $body;
    }
}

?>
