<?php
/**
 * Работа с базой MySQL. 
 *
 * __construct($user = "root", $pass = "111", $base = "mysql", $host = "localhost", $connect_timeout = 9, $charset = 'utf8').
 *
 *get_data_from_sql($query_text)-Выполняет запрос и помещает результат в двумерный массив вида array(№ строки->(array(имя_столбца[либо №]->значение)))
 *
 *	$data_array=$query->get_data_from_sql('SELECT * FROM locales_source');	
 */
class Ser_Sql_query{

		private $connect;             //Объект соединения (mysqli)
		public  $connect_state;		  //Статус соединения (1/0)
		
		//Проверка и инициализация подключения к Базе.
		function __construct($user,$pass,$base,$host,$connect_timeout,$charset){ 
		
				$this->connect = mysqli_init();  
				if($connect_timeout){$this->connect->options(MYSQLI_OPT_CONNECT_TIMEOUT, ((int)$connect_timeout));}
				$this->connect->real_connect($host, $user, $pass, $base);
					
				if ($this->connect->connect_errno){
					echo('<br /><font color="red">Ошибка подключения SQL: ' . $this->connect->connect_error . '</font><br />');
					$this->connect_state = 0;}
				else{$this->connect_state = 1;}
				
					//установка кодировки базы для real_escape_string.
					if ($charset){
					if(!$this->connect->set_charset($charset)){printf("Ошибка при загрузке набора символов: %s\n", $this->connect->error);} 
					else {printf("Текущий набор символов: %s\n", $this->connect->character_set_name());}}		
		}
		
		//Close connection.		
		function __destruct(){$this->connect->close();}	
		
		
		//Get results from base. Return data array(row_number->field_data_array[])
		public function get_data_from_sql($query_text){
						
				if ($this->connect_state === 1) {
						$result = $this->connect->query($query_text);	
						
						if ($this->connect->errno){echo '<font  color= "red">Ошибка запроса: ' . $this->connect->error . '</font><br />';return(0);}
						else {	
								if(is_object($result) && $result->num_rows){
										echo('Запрос выполнен!<br />');
										$data_array = $this->obj_result_to_array($result);			
										$result->free();
										return($data_array);	
								}
								else{
									echo ('Запрос выполнен без возврата данных!<br />'); 
									return(0);
								}						
						}	
				}			
		}	
		
		//Получаем данные из объекта mysqli в двумерный массив.
		private function obj_result_to_array($result) {  
							
			$result->data_seek(0);
			while ($row = $result->fetch_array(MYSQLI_ASSOC/* MYSQLI_NUM */)){
					$data_array[] = $row;
			}		
			return($data_array);
		}
		

		/**
		 * Дополнительные возможности работы с результатом MySQL.
		 */		
		//Вывод в Html результата запроса.	
		public function sql_query_to_table_html($data_array) {
			if(is_array($data_array)){
					echo('<style>#sql_to_html_table{border-collapse: collapse;}
						td,th{padding:1px; border:1px solid black; text-align:left; font: 10pt \'Times New Roman\'}
						th{background: #b0e0e6;}</style><br />
						<table id="sql_to_html_table">');	
				
								$name_row_check = 0;
								$name_row='';
								$tab='';
					
								foreach($data_array as $row){
									
									if($name_row_check==0){$name_row=$name_row.'<tr>';} 
									$tab=$tab.'<tr>';
									
										foreach($row as $field => $value){
											if($name_row_check==0){$name_row=$name_row.'<th>'.$field.'</th>';}
											$tab=$tab.'<td>'.$value.'</td>';	
										}	
										
									if($name_row_check==0){$name_row=$name_row.'</tr>';} $name_row_check = 1;
									$tab=$tab.'</tr>';
								}			
					
					echo($name_row.$tab);
					echo('</table><br />');	
			}
		}
		
		//Создание формы html и запрос в базу на основании текста из этой формы.	
		public function sql_query_from_http_form() {	
				if (!isset($_REQUEST['sql_query_from_form'])) {
						echo($this->create_sql_query_form('SELECT * FROM user'));} 
				else {
						if ($_REQUEST['sql_query_from_form']<>'') {
								echo($this->create_sql_query_form($_REQUEST['sql_query_from_form']));
								$Res = $this->get_data_from_sql($_REQUEST['sql_query_from_form']);
								$this->sql_query_to_table_html($Res);
						} 	
						else {
								echo($this->create_sql_query_form('SELECT * FROM user'));
								echo("Запрос пустой. Поле заполнено шаблоном.<br />");
						}	
				}
		}

		//Создание html формы запроса Sql.		
		private function create_sql_query_form($text) {   
				echo('<form action='.$_SERVER['SCRIPT_NAME'].' method=post>');
				echo('<style>.query_input{height:300px; width: 600px;} </style>');
				echo('<p><b>Введите текст запроса:</b></p>');
				echo('<textarea name="sql_query_from_form"  rows="10" cols="45" class="query_input">'.$text.'</textarea><br />');
				echo('<input type=submit value="Выполнить запрос" />');
				echo('</form>');
		}	
		
}
?>

