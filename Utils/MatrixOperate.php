<?php
/**
* 矩阵运算
*/
namespace Utils;
class MatrixOperate 
{
	
	public function operate($A = array(),$operate = '+',$B = array())
	{
		switch ($operate) {
			case '+':
			case '-':
				if (is_numeric($B)) {
					foreach ($A as $row => $value) {
						if (is_array($value)) {
							foreach ($value as $cow => $v) {
								eval('$A[$row][$cow] '.$operate.'= $B;');
							}
						}else{
							eval('$A[$row] '.$operate.'= $B;');
						}
					}
					return $A;
				}
				if (count($A)!=count($B)) {
					echo 'error';exit();
				}
				foreach ($A as $row => $value) {
					if (is_array($value)) {
						foreach ($value as $cow => $v) {
							eval('$A[$row][$cow] '.$operate.'= $B[$row][$cow];');
						}
					}else{
						eval('$A[$row] '.$operate.'= $B[$row];');
					}
				}
				return $A;
				break;
			case '/':
				if (is_numeric($B)) {
					foreach ($A as $row => $value) {
						foreach ($value as $cow => $v) {
							$A[$row][$cow]/=$B;
						}
					}
					return $A;
				}
			case '*':
				if (is_numeric($B)) {
					foreach ($A as $row => $value) {
						foreach ($value as $cow => $v) {
							$A[$row][$cow]*=$B;
						}
					}
					return $A;
				}
				$temp = array();
				$c = count($B[0]);
				foreach ($A as $row => $value) {
					for ($i=0; $i < $c; $i++) { 
						$t = 0;
						foreach ($value as $cow => $v) {
							$t+=$A[$row][$cow]*$B[$cow][$i];
						}
						$temp[$row][$i] = $t;
					}
				}
				return $temp;
			case 'T'://矩阵转置
				foreach ($A as $key => $value) {
					foreach ($value as $k => $v) {
						$B[$k][$key] = $A[$key][$k];
					}
				}
				return $B;
			case '-1'://矩阵求逆
				$x = array();
				$B = array();
				foreach ($A as $key => $value) {
					$y = array_fill(0, count($A), 0);
					$y[$key] = 1;
					$B[] = $this->getMultyTheta($A,$y);
				}
				$B = $this->operate($B,'T');
				return $B;

				break;
			
			default:
				break;
		}
	}
	/**
	 * [getMultyTheta 多元一次方程求解]
	 * @param  [type] &$x [description]
	 * @param  [type] &$y [description]
	 * @return [type]     [description]
	 */
	function getMultyTheta(&$x,&$y){
		$length = count($y);
		$tmep = array();
		foreach ($x as $key => $value) {
			
			$a = $x;
			$b = $y;
			$tmep[$key] = $this->getMultyX($a,$b,$key);
		}
		return $tmep;
	}
	/**
	 * [getMultyX 多元一次方程求解]
	 * https://wenku.baidu.com/view/99bf58cf050876323112120c.html
	 * @param  [type]  &$x    [description]
	 * @param  [type]  &$y    [description]
	 * @param  integer $index [description]
	 * @return [type]         [description]
	 */
	function getMultyX(&$x,&$y,$index = 0,$debugLogicTime = 5){
		$lengx = count($x[0]);

		if ($index<$lengx-1) {
			foreach ($x as $key => &$value) {
				$temp = $x[$key][$lengx-1];

				$x[$key][$lengx-1] = $x[$key][$index];

				$x[$key][$index] = $temp;
			}
			$index = $lengx;
		}

		// if ($debugLogicTime==1||$debugLogicTime==0) {
			// echo $lengx;
			// echo '0:'."\r\n";
			// print_r($x);
			// print_r($y);
		// }
		
		//等比例缩放系数为1
		//如果系数为零,或者为1，则，不缩放
		foreach ($x as $key => &$value) {
			if($value[0]==0||$value[0]==1)continue;
			$flag = $value[0];
			$y[$key] = $y[$key]/$flag;
			foreach ($value as $k => &$v) {
				$v = $v/$flag;
			}
		}
		// if ($debugLogicTime==1||$debugLogicTime==0) {
			// echo '1:'."\r\n";
			// print_r($x);
			// print_r($y);
		// }
		//矩阵相减至所有参数消除
		foreach ($x as $key => &$value) {

			if ($key!=0&&!isset($x[$key+1])||$value[0]==0) {
				unset($x[$key]);
				unset($y[$key]);
				continue;
			}

			$value = $this->operate($value,'-',$x[$key+1]);

			$y[$key] -= $y[$key+1];
		}


		// if ($debugLogicTime==1||$debugLogicTime==0) {
			// echo '2:'."\r\n";
			// print_r($x);
			// print_r($y);
			// if ($debugLogicTime==1)exit();
		// }
		foreach ($x as $key => &$value) {
			unset($value[0]);
			$value = array_values($value);
		}
		//消元 知道为一个系数时返回求得系数
		if (count($y)>1) {
			$debugLogicTime++;
			$this->getMultyX($x,$y,$index,$debugLogicTime);
		}
		return $y[0]/$x[0][0];
	}

	function getMultyEquation($x,$y,$index = 0,&$temp){

		if (!isset($y[$index+1])) {
			$temp[$index] = 1;
			return 0;
		}

		$temp[$index] = ($y[$index]-getMultyEquation($x,$y,$index+1,$temp))/floatval($x[$index]);

		return $temp[$index];
		$y = $temp0*x0 +$temp1*x1;
		$y = $temp0*x0 +$temp1*x1;
	}

	function sunX($x1,$x2){
		$x1 = explode('+', $x1);
		$x2 = explode('+', $x2);
		$x1[0]+=$x2[0];
		$x1[1]+=$x2[1];



	}

}

?>