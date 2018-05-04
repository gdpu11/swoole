<?php
/**
 * gradient descent
 * 梯度下降测试
 * 正规方程测试
 */
/*E(0~+) 表示0到正无穷求和

h(x) = ax+b;
J(x) = 1/(2*m)*E(0~+)* (h(x)-y)^2 

J(x) = 1/(2*m)*E(0~+)* (ax+b-y)^2 

a = a- $Learningrate*Ja(x)';
b = b- $Learningrate*Jb(x)';

Ja(x) = E(0~+)*(ax+b-y)*x 

Jb(x) = E(0~+)*(ax+b-y)*1

*/
namespace Utils;
class GradientDescent 
{
	private $X = array();
	private $Y = array();
	private $T = array();//theta
	private $S_T = array();//Soucetheta, 当learningrate太大时，重新开始计算
	private $L_R = array();//Learningrate

	private $C_V = array();//costValue  损失函数的值

	private $times = array();//梯度下降次数

	function __construct($x,$y,$t,$Learningrate,$costValue = 10,$times = 100)
	{
		$this->X = $x;
		$this->Y = $y;
		$this->T = $t;
		$this->S_T = $t;
		$this->L_R = $Learningrate;
		$this->C_V = $costValue;
		$this->times = $times;
		// $this->feature_scaling();
	}


	
	//Locally weighted regression(局部加权回归)
	//局部加权回归，当要处理x时：
	// 1)       检查数据集合，并且只考虑位于x周围的固定区域内的数据点
	// 2)       对这个区域内的点做线性回归，拟合出一条直线
	// 3)       根据这条拟合直线对x的输出，作为算法返回的结果
	//批量梯度下降法（Batch Gradient Descent，简称BGD）:每次更新θ都是所有样本
	//随机梯度下降法（Stochastic Gradient Descent，简称SGD）：每次更新θ只用一个样本
	//小批量梯度下降法（Mini-batch Gradient Descent，简称MBGD）：每次更新样本用部分样本 一般是10个 或者20等

	function BGD(){
		$t1 = microtime(true);

		$result  = $this->get_J_sum();

		$i=1;

		$flag = 1;
		
		/*
		开始梯度求解
		 */
		while ($result>$this->C_V&&$i++<$this->times&&$flag) {
			// $this->out_F_x($result);

			$this->T = $this->get_Theta();

			//α  选择不当时，除以2
			if ($this->get_J_sum()>$result) {
				$this->T = $this->S_T;
				$this->L_R = $this->L_R/3;
				$result  = $this->get_J_sum();
				continue;
			}
			// $this->T = $this->S_T;
			/*if (abs($this->get_J_sum()-$result)<0.000000000001) {
				$this->L_R = $this->L_R*2;
				$result  = $this->get_J_sum();
				continue;
			}*/
			if (abs($this->get_J_sum()-$result)<0.000000000001) {

				echo 'The next iteration change too small for the last iteration,lest then 0.000000000001 .done'."\r\n";
				$flag = 0;
				
			}

			$result  = $this->get_J_sum();
		}

		if ($result<=$this->C_V) {
			echo 'Cost function Get The Ideal value .done'."\r\n";
		}

		if ($i-1==$this->times) {
			echo 'times over .done'."\r\n";
		}

		echo "run $i times\r\n";
		echo "Learningrate:$this->L_R\r\n";
		$t2 = microtime(true);
		echo 'run time:'.round($t2-$t1,3)."\r\n";
		echo 'Now memory_get_usage: ' . memory_get_usage() ."byte\r\n";
		$this->out_F_x($result);
	}

	/**
	 * [out_F_x 输出函数表达式]
	 * @param  [type] $result [description]
	 * @return [type]         [description]
	 */
	function out_F_x($result){
		$out = "F(x) = ";
		
		foreach ($this->T as $key => $value) {
			if ($key==0) {
				$x = '';
			}else{
				$x = 'x';
			}
			$out .="{$value}{$x}+";
		}
		echo trim($out,'+')."\r\nCost value is ".$result."\r\n";
	}

	/**
	 * [feature_scaling 特征缩放]
	 * 数据量大时，加快执行效率
	 * @return [type] [description]
	 */
	function feature_scaling(){

		print_r($this->X);

		$max = $min = array_fill(0,count($this->T),0);


		foreach ($this->X as $key => $value) {
			foreach ($value as $k => $v) {
				if ($key==0) {
					$max[$k] = $v;
					$min[$k] = $v;
				}else{
					$max[$k] = $max[$k]>$v?$max[$k]:$v;
					$min[$k] = $min[$k]<$v?$min[$k]:$v;	
				}
			}
			if ($key==0) {
				$max[$k+1] = $this->Y[$key];
				$min[$k+1] = $this->Y[$key];
			}else{
				$max[$k+1] = $max[$k+1]>$v?$max[$k+1]:$v;
				$min[$k+1] = $min[$k+1]<$v?$min[$k+1]:$v;	
			}
		}
		
		foreach ($this->X as $key => &$value) {
			foreach ($value as $k => &$v) {
				if ($k==0) {
					continue;
				}
				$v = ($v-($max[$k]+$min[$k])/2)/$max[$k];
			}
			$this->Y[$key] = ($this->Y[$key]-($max[$k+1]+$min[$k+1])/2)/$max[$k+1];
		}
		print_r($min);
		print_r($max);
		print_r($this->X);
		// exit();
	}

	/**
	 * [get_J_sum 计算损失函数值]
	 * 即  J(θ) 的值
	 * @return [type] [description]
	 */
	function get_J_sum(){
		$sum = 0;
		foreach ($this->X as $key => $value) {
			// echo 'value::::'.($a*$value['0']+$b-$value['1'])."\r\n";
			$sum+= pow($this->get_h_Theta($key), 2) ;
		}
		// echo 'Jsum:'.$sum."\r\n";
		return $sum/count($this->X)/2;
	}


	/**
	 * [get_Theta 计算下一轮θ值]
	 *Ja(x) = E(0~+)*(ax+b-y)*x
	 * @return [type] [description]
	 */
	function get_Theta(){
		$newTheta = $this->T;
		// $sum = array();
		$sum = array_fill(0,count($this->T),0);
		//获取每个θ的偏导数
		foreach ($this->T as $key => $value) {
			$sum[$key] =  $this->get_J_Theta($key);
		}
		//分别更新各个θ
		foreach ($sum as $key => $value) {
			$newTheta[$key] = $this->T[$key]-($this->L_R*$value);
		}
		return $newTheta;
	}
	/**
	 * [get_h_Theta 计算下标为index的假设函数 Hθ(x) 关于θ的值]
	 * 其中x0 = 1;
	 * h(x) = θ0*x0+θ1*x1.....+θn*xn;
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	function get_h_Theta($index){
		$sum = 0;
		foreach ($this->X[$index] as $k => $v) {
			// echo '$this->T[$k]*$v:'."{$this->T[$k]}*{$v}"."\r\n";
			$sum += $this->T[$k]*$v;
		}
		$sum -= $this->Y[$index];
		return $sum;
	}

	/**
	 * [get_J_Theta 计算θ关于J的偏导]
	 * Jθ(x) = E(0~+)*(hθ(x)-y)*x
	 * @param  [type] $index [description]
	 * @return [type]        [description]
	 */
	function get_J_Theta($index){
		$sum = 0;

		foreach ($this->X as $key => $value) {
			// echo 'Theta'.$index.':'.$value[$index]."\r\n";
			$sum += $this->get_h_Theta($key)*$value[$index];
		}
		// exit();
		return $sum;
	}

}

?>