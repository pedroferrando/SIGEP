<?php
class Paginacion
{	
	private $page; //Pagina actual
	private $from; //Numero de registro desde donde iniciar la busqueda
	private $quantity; //Catidad de registros por página
	private $totalRows; //Total de registros a paginar
	private $numPages; //Numero total de paginas
	private $puri; //Parte de la uri a agregar a la url inicial
	private $url; //Url de la página que se desea hacer la paginación
	private $classLink;
	
	
	public function __construct($quantity, $page) {
			
		//Validamos la entrada de datos
		settype($page, "integer");
		settype($quantity, "integer");
		$this->page = ($page > 0) ? $page : 0;
		$this->quantity = ($quantity > 0) ? $quantity : 0;
		$this->from = $page * $quantity;
		
	}
	
	public function getFrom()
	{
		return $this->from;
	}
        
	
        /* 
        $url es el nombre del archivo al que apuntara el link devuelto
        $param_url es un array con los argumentos que se pasan en la url
        */
	public function generaPaginacion($totalRows, $back, $next, $url, $param_url, $class = "")
	{
		settype($totalRows, "integer");
		$this->totalRows = $totalRows;
		
		$this->classLink = $class;
		
                $this->url = $url; 
		$this->numPages = ceil($totalRows/$this->quantity);
		
		if ($this->page > 0) {
			$this->puri = $this->page - 1;
                        $param_url['page'] = $this->puri;
			//echo "<a href=\"$this->url&page=$this->puri\">$back</a>";
			echo "<a class='".$class."' href='".encode_link($this->url,$param_url)."'>$back</a>";
		}
		
		if($this->numPages > 1) {
                    for ($i = 0; $i < $this->numPages; $i++) {
                        if ($i == $this->page) {
                            $paginaN = $i + 1;
                            echo "<span class='".$class." actualPage'>$paginaN</span>";
                        }
                        elseif($i == $this->page + 1 || $i == $this->page + 2 || $i == $this->page - 1
                        || $i == $this->page - 2 || $i == 0 || $i == ($this->numPages - 1)){
                            //$page + 1, $page +2 son los numeros que se desea ver por delante del actual
                            //$page -1, $page -2 son los numeros (Links) a ver por detras del actual
                            //Esto se puede modificar como se desee
                            $paginaN = $i + 1;
                            $param_url['page'] = $i;
                            //echo " <a class=\"$class\" href=\"".$this->url."page=$i\">$paginaN</a> ";
                            echo " <a class='".$class."' href='".encode_link($this->url,$param_url)."'>$paginaN</a> ";
                        }
                        elseif ($i == $this->page - 3) {
                            echo "<span>...</span>";
                        }
                        elseif ($i == $this->page + 3) {
                            echo "<span>...</span>";
                        }
                    }
		}
		
		if ($this->page < $this->numPages - 1) {
                    $this->puri = $this->page + 1;
                    $param_url['page'] = $this->puri;
                    //echo "<a href=\"".$this->url."page=$this->puri\">$next</a>";
                    echo "<a class='".$class."' href='".encode_link($this->url,$param_url)."'>$next</a>";
		}
	}
	
}
?>