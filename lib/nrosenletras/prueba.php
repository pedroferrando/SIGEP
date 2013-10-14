<?php
  include_once("class.nroenletraver2.php");
    $objeto=new NroEnLetra($_POST['numero'],$_POST['f']);
  echo $objeto->getLetra();
  
?>