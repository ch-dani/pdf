<?php


namespace App\Custom;

class P2E{
	
	private $rt = array();
	private $tables = array();
	private $return = array();
	
	
	public function shit($tables=array()){
		echo "<pre>";
		$this->tables = $tables;

		
		$return_table = array();
		
		$shell = "pdftotext -bbox-layout -f 1 -l 1 -x 80 -y 141 -W 470 -H 208 /var/www/html/homes/public/uploads/pdf/c575f173-6d4a-85bf-f80a-774099faad87_edit.pdf -";
		$xml = shell_exec($shell);
		preg_match("/\<doc\>(.*)\<\/doc\>/ms", $xml, $matches);
		if(empty($matches)){
			return false;
		}
		$xml = "<?xml version='1.0' standalone='yes'?>\r\n".$matches[0];




		$x1ml = '<?xml version="1.0" standalone="yes"?>
<doc>
  <page width="612.000000" height="792.000000">
    <flow>
      <block xMin="90.000000" yMin="241.849920" xMax="138.024000" yMax="254.501760">
        <line xMin="90.000000" yMin="241.849920" xMax="138.024000" yMax="254.501760">
          <word xMin="90.000000" yMin="241.849920" xMax="108.337440" yMax="254.501760">Low</word>
          <word xMin="110.876640" yMin="241.849920" xMax="138.024000" yMax="254.501760">Vision</word>
        </line>
      </block>
    </flow>

</page>
</doc>';


		
		$doc = new \SimpleXMLElement($xml);
		
		
		//TODO fix для двух и более страниц
		
		$page = 0;
		
		$rt = array();
		$current_row = 0;
		$current_cell = 0;
		$cols= array();
		foreach($doc->page->flow as $flow){
			foreach($flow->block as $block){
				$ba = $block->attributes();
				$ba = $this->normalizePosAttrs($ba);
				$table_n = $this->blockInTable($ba, $page);
				if($table_n!=-1){
					if(!isset($this->return[$table_n])){
						$this->return[$table_n] = array();
					}
					$cell = "";
					$prev_line_y = -1;
					foreach($block->line as $line){
						$la = $this->normalizePosAttrs($line->attributes());
						//$word = ((String)$line->word);
						foreach($line->word as $word){
							if($prev_line_y!=-1 and $la['y']!= $prev_line_y){
							//TODO добавить /r/n
								$cell .= " ".$word;
							}else{
								$cell .= " ".$word;
							}
						}
						$prev_line_y = $la['y'];
					}
					
					$cell = trim($cell);
					if(!isset($this->return[$table_n][$ba['y']])){
						$this->return[$table_n][$ba['y']] = [];
					}
					
					$cols[$ba['x']] = $ba['x'];
					
					$this->return[$table_n][$ba['y']][$ba['x']] = $cell;
				}
				
				
				
//				if($cr = $this->rowExist($ba['top'])){
//					exit("row exist");
//				}
//				foreach($block as $line){
//					$la = ["xMin"=>(int)$line->attributes()['xMin'], "yMin"=>(int)$line->attributes()['yMin']] ; //[0]['@attributes'];
//					
//					
//				}
			
			}
		}
		ksort($this->return);
		var_dump($this->return);
		exit();
		
		foreach($this->return as &$t){
			ksort($t);
		}
		
		$csv = "";
		foreach($this->return as $row){
		
			var_dump($row);
		
		
			exit();
		
			foreach($row as $col){
			
				$csv .= implode("|", $col). ";";
			}
			$csv .= "\r\n";
		}
		echo "<pre>";
		
		exit($csv);
		
		echo "<pre>";
		var_dump($cols);
		var_dump($this->return);
		exit();
		
		exit("shit");
	}
	
	
	private function blockInTable($block=false, $page=1, $dbg=false){
		foreach($this->tables[$page] as $k=>$table){
			//TODO скорее всего сравниваю херню
			if((int)$table['x']<=$block['x'] and (int)$table['y']>=$block['y'] and $table['w']>=$block['w'] and $table['h']>=$block['h']){
				return $k;
			}
		}
		return -1;
	}
	
	
	private function normalizePosAttrs($attrs=false){
	
		return ["x"=>(int)$attrs['xMin'], 
				"y"=>(int)$attrs['yMin'], 
				"h"=>(int)$attrs['yMax']-(int)$attrs['yMin'],
				"w"=>(int)$attrs['xMax']-(int)$attrs['xMin'],
				];
	}
	
	
	private function rowExist($block_min = false){
		foreach($this->rt as $k=>$row){
			if($k==$block_min){
				return $this->rt[$k];
			}
		}
		return false;
	}
	
	
}

