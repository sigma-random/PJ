<?php

	//�����������ʱ��
	class Timer {  

		private $StartTime = 0;//�������п�ʼʱ��
		private $StopTime  = 0;//�������н���ʱ��
		private $TimeSpent = 0;//�������л���ʱ��

		function start(){//�������п�ʼ
			$this->StartTime = microtime();  
		}  

		function stop(){//�������н���
			$this->StopTime = microtime();  
		}  

		function spent(){//�������л��ѵ�ʱ��
			if ($this->TimeSpent) {  
				return $this->TimeSpent;  
			} else {
			 list($StartMicro, $StartSecond) = explode(" ", $this->StartTime);
			 list($StopMicro, $StopSecond) = explode(" ", $this->StopTime);
				$start = doubleval($StartMicro) + $StartSecond;
				$stop = doubleval($StopMicro) + $StopSecond;
				$this->TimeSpent = $stop - $start;
				return substr($this->TimeSpent,0,8);//."��";//���ػ�ȡ���ĳ�������ʱ���
			}  
		}

	}  


?>