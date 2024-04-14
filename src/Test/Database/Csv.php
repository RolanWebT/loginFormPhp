<?php 
			namespace Test\Database;

			class Csv implements Driver {
    
				use Traits\Quote;
				
				public function __construct($str_database='')
				{
					//echo "\$str_database: " . $str_database . "<br>";
					
if(DEBUG_CC) 		echo "<p class='debug class'>🛠 <b>Line " . __LINE__ .  "</b>: Aufruf " . __METHOD__ . "()  (<i>" . basename(__FILE__) . "</i>)</p>\n";						

					if(empty($str_database)){
						trigger_error('No database base folder given.', E_USER_ERROR);
						return null;
					}
					
					if(!file_exists($str_database)){
						trigger_error('Given database base folder not there.', E_USER_ERROR);
						return null;
					}
					
					if(!is_dir($str_database)){
						trigger_error('Given database base folder is not a directory.', E_USER_ERROR);
						return null;
					}
					
					$this->setFolder($str_database);
				}
			
				public function buildSelect()
				{
					return new Csv\Select;
				}
				
				public function buildDelete()
				{
					return new Csv\Delete;
				}
				
				public function buildInsert()
				{
					return new Csv\Insert;
				}
				
				public function buildUpdate()
				{
					return new Csv\Update;
				}
				
				private function fetch(\Test\Database\Query $obj_query, $boo_headOnly=false)
				{
					if(!($obj_query instanceof Csv\Select)){
						trigger_error('Invalid argument, expected Test\Database\Csv\Select', E_USER_ERROR);
						return false;
					}
					
					$str_from = $obj_query->__from;
					
					if(empty($str_from)){
						trigger_error('Query object missing FROM clause', E_USER_ERROR);
						return false;
					}
					
					$str_csvFile = realpath($this->getFolder().DIRECTORY_SEPARATOR.$str_from.'.csv');
					if(!file_exists($str_csvFile)){
						trigger_error(sprintf('Invalid table object "%s"', $str_from), E_USER_ERROR);
						return false;
					}
					
					$arr_head = [];
					$arr_data = [];
					$i = 0;
					if(($handle = fopen($str_csvFile, "r")) !== FALSE){
						while(($arr_row = fgetcsv($handle, 1000, ",")) !== FALSE){
							if(!$i++){
								$arr_head = $arr_row;
								if($boo_headOnly) return $arr_head;
							}
							else $arr_data[] = array_combine($arr_head, $arr_row);
						}
						fclose($handle);
					}
					return $arr_data;
				}
			
				private function validate($arr_row, $arr_wheres=[], $arr_alias=[])
				{
					if(empty($arr_row))
						return false;
					$boo_returnResult = true;
					foreach($arr_wheres as $arr_where){
						foreach($arr_where as $str_andOr => $arr_whereClause){
							$arr_keys = array_keys($arr_whereClause);
							$str_col = end($arr_keys);
							$str_val = $arr_whereClause[$str_col];
							$boo_result = $arr_row[$str_col] == (substr($str_val, 0, 1)===':'?$arr_alias[substr($str_val, 1)]:$str_val);
							
							switch($str_andOr){
								case 'and':
									$boo_returnResult = $boo_returnResult && $boo_result;
									break;
								case 'or':
									$boo_returnResult = $boo_returnResult || $boo_result;
									break;
							}
						}
					}
					return $boo_returnResult;
				}
			
				public function fetchRow(\Test\Database\Query $obj_query, array $arr_alias=[])
				{
					$arr_data = $this->fetch($obj_query);
					if($arr_data===false) return [];
					
					$arr_cols = $obj_query->__cols;
					$arr_wheres = $obj_query->__where;
					$arr_return = [];
					foreach($arr_data as $arr_row){
						if(!empty($arr_cols))foreach($arr_cols as $str_col)
							$arr_return[$str_col] = $arr_row[$str_col];
						else $arr_return = $arr_row;
						if(empty($arr_wheres))
							return $arr_return;
						if($this->validate($arr_row, $arr_wheres, $arr_alias))
							return $arr_return;
					}
					return [];
				}
			
				public function fetchCol(\Test\Database\Query $obj_query, array $arr_alias=[])
				{
					$arr_cols = $obj_query->__cols;
					if(empty($arr_cols)){
						trigger_error('Expect column to fetch, none given', E_USER_ERROR);
						return false;
					}
					$str_col = end($arr_cols);
					$arr_data = $this->fetch($obj_query);
					if($arr_data===false) return [];
					$arr_wheres = $obj_query->__where;
					
					$arr_return = [];
					foreach($arr_data as $arr_row){
						if(empty($arr_wheres))
						$arr_return[] = $arr_row[$str_col];
						elseif($this->validate($arr_row, $arr_wheres, $arr_alias))
							$arr_return[] = $arr_row[$str_col];
					}
					return $arr_return;
				}
			
				public function fetchOne(\Test\Database\Query $obj_query, array $arr_alias=[])
				{
					$arr_cols = $obj_query->__cols;
					if(empty($arr_cols)){
						trigger_error('Expect column to fetch, none given', E_USER_ERROR);
						return false;
					}
					$str_col = end($arr_cols);
					
					return $this->fetchRow($obj_query)[$str_col];
				}
			
				public function fetchAll(\Test\Database\Query $obj_query, array $arr_alias=[])
				{
					$arr_return = $this->fetchAssoc($obj_query, $arr_alias);
					if(is_array($arr_return) && !empty($arr_return)){
						foreach($arr_return as $i => $arr_row)
							$arr_return[$i] = array_merge($arr_row, array_values($arr_row));
					}
					return $arr_return;
				}
			
				public function fetchAssoc(\Test\Database\Query $obj_query, array $arr_alias=[])
				{
					$arr_data = $this->fetch($obj_query);
					if($arr_data===false) return [];
					
					$arr_cols = $obj_query->__cols;
					$arr_wheres = $obj_query->__where;
					$arr_return = [];
					foreach($arr_data as $arr_row){
						$arr_returnRow = [];
						if(!empty($arr_cols))foreach($arr_cols as $str_col)
							$arr_returnRow[$str_col] = $arr_row[$str_col];
						else $arr_returnRow = $arr_row;
						if(empty($arr_wheres))
							$arr_return[] = $arr_returnRow;
						elseif($this->validate($arr_row, $arr_wheres, $arr_alias))
							$arr_return[] = $arr_returnRow;;
					}
					return $arr_return;
				}
			
				public function execute(\Test\Database\Query $obj_query, array $arr_alias=[])
				{
					$str_table = $obj_query->__table;
					
					$arr_data = $this->fetch($this->buildSelect()->from($str_table));
					$arr_head = empty($arr_data)?$this->fetch($this->buildSelect()->from($str_table), true):array_keys($arr_data[0]);
					if($arr_data === false) return false;
					
					$str_csvFile = realpath($this->getFolder().DIRECTORY_SEPARATOR.$str_table.'.csv');
					$fp = fopen($str_csvFile, 'w');
					fputcsv($fp, $arr_head);
					
					if($obj_query instanceof Csv\Update){
						$arr_wheres = $obj_query->__where;
						$arr_set = $obj_query->__set;
						if(array_key_exists('id', $arr_set))unset($arr_set['id']);
						foreach($arr_data as $arr_row){
							if($this->validate($arr_row, $arr_wheres, $arr_alias)){
								foreach($arr_set as $str_col => $str_val)if(array_key_exists($str_col, $arr_row))
									$arr_row[$str_col] = (substr($str_val, 0, 1)===':'?$arr_alias[substr($str_val, 1)]:$str_val);
							}
							fputcsv($fp, $arr_row);
						}
					}
					
					if($obj_query instanceof Csv\Delete){
						$arr_wheres = $obj_query->__where;
						foreach($arr_data as $arr_row){
							if(!$this->validate($arr_row, $arr_wheres, $arr_alias))
								fputcsv($fp, $arr_row);
						}
					}
					
					if($obj_query instanceof Csv\Insert){
						$arr_set = $obj_query->__set;
						$id = 0;
						foreach($arr_data as $arr_row){
							fputcsv($fp, $arr_row);
							$id = array_key_exists('id', $arr_row)?max($arr_row['id'], $id):$id;
						}
						$arr_put = [];
						if(in_array('id', $arr_head))
							$arr_put['id'] = ++$id;
						if(array_key_exists('id', $arr_set))unset($arr_set['id']);
						foreach($arr_head as $str_col)
							if(array_key_exists($str_col, $arr_set))
								$arr_put[$str_col] = (substr($arr_set[$str_col], 0, 1)===':'?$arr_alias[substr($arr_set[$str_col], 1)]:$arr_set[$str_col]);
							else $arr_put[$str_col] = $str_col=='id'?$arr_put[$str_col]:NULL;
						fputcsv($fp, $arr_put);
					}
					
					fclose($fp);
					return true;
				}
			
				private $str_folder;
				
				/**
				*    Set the str_folder value
				*    @param string $str_folder
				*/
				private function setFolder($str_folder)
				{
					$this->str_folder = $str_folder;
				}
				
				/**
				*    Returns the str_folder value.
				*    @return string
				*/
				private function getFolder()
				{
					return $this->str_folder;
				}
			}