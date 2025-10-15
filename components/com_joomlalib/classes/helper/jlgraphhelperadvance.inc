<?php
/**
 * Bezier
 * 
 * @package JL
 * @subpackage JLGrahp
 * @since 1.1.0
 */
class JLGRaphHelperBezier {

	/**
	 * Array to hold base x data
	 *
	 * @var array
	 */
	var $datax = array();
	/**
	 * Array to hold base y data
	 *
	 * @var array
	 */
	var $datay = array();
	/**
	 * Count of origenal data points
	 *
	 * @var integer
	 */
	var $n=0;

	/**
	 * Construct stores data to be used and set attraction factor
	 *
	 * @param array JLGraph data array
	 * @param integer attraction factor
	 */
	function JLGRaphHelperBezier($data, $factor = 1) {
		$data = JLGraphHelper::_stripDataSet($data);
		$this->n = count($data);
		$idx=0;
		foreach($data as $x => $y) {
			for ($i = 0; $i < $factor; $i++) {
				$this->datax[$idx] = $x;
				$this->datay[$idx] = $y;
				$idx++;
			}
		}
	}

	/**
	 * Retrieves the new data
	 *
	 * @param integer number of steps
	 * @return array a JLGraph data array
	 */
	function getNewData($steps) {
		$data = array();
		for ($i = 0; $i < $steps; $i++) {
			list($x, $y) = $this->getPoint((double) $i / (double) $steps);
			$data[] = array('name' => $x, 'value' => $y);
		}
		$data[] = array('name' => end($this->datax), 'value' => end($this->datay));
		return $data;
	}

	/**
	 * Calculates a new point
	 *
	 * @param double $mu
	 * @return array containing x and y value
	 */
	function getPoint($mu) {
		$n = $this->n - 1;
		$newx = 0.0;
		$newy = 0.0;
		$muk = 1.0;
		$munk = (double) pow(1-$mu,(double) $n);

		for ($k = 0; $k <= $n; $k++) {
			$nn = $n;
			$kn = $k;
			$nkn = $n - $k;
			$blend = $muk * $munk;
			$muk *= $mu;
			$munk /= (1-$mu);
			while ($nn >= 1) {
				$blend *= $nn;
				$nn--;
				if ($kn > 1) {
					$blend /= (double) $kn;
					$kn--;
				}
				if ($nkn > 1) {
					$blend /= (double) $nkn;
					$nkn--;
				}
			}
			$newx += $this->datax[$k] * $blend;
			$newy += $this->datay[$k] * $blend;
		}

		return array($newx, $newy);
	}
}

/**
 * Spline
 * 
 * @package JL
 * @subpackage JLGraph
 * @since 1.1.0
 */
class JLGraphHelperSpline {
	
	/**
	 * Holds origenal x data
	 *
	 * @var array
	 */
	var $xdata 	= array();
	/**
	 * Holds origenal y data
	 *
	 * @var array
	 */
	var $ydata 	= array();   
	/**
	 * Holds 2nd derivate
	 *
	 * @var unknown_type
	 */
	var $y2 	= array();
	/**
	 * Count of origenal data points
	 *
	 * @var integer
	 */
	var $n		= 0;

	/**
	 * Construct
	 *
	 * @param array JLGraph data array
	 * @return JLGraphHelperSpline
	 */
	function JLGraphHelperSpline($data) {
		$data = JLGraphHelper::_stripDataSet($data);
		foreach($data as $x => $y){
			$this->xdata[] = $xdata = $x;
			$this->ydata[] = $ydata = $y;
		}
		$n = count($this->xdata);
		$this->n = $n;

		$this->y2[0]    = 0.0;
		$this->y2[$n-1] = 0.0;
		$delta[0] 		= 0.0;

		for($i=1; $i < $n-1; ++$i) {
			$d = ($xdata[$i+1]-$xdata[$i-1]);
			if( $d == 0  ) {
				die('Invalid input data for spline. Two or more consecutive input X-values are equal.');
			}
			$s = ($xdata[$i]-$xdata[$i-1])/$d;
			$p = $s*$this->y2[$i-1]+2.0;
			$this->y2[$i] = ($s-1.0)/$p;
			$delta[$i] = ($ydata[$i+1]-$ydata[$i])/($xdata[$i+1]-$xdata[$i]) - ($ydata[$i]-$ydata[$i-1])/($xdata[$i]-$xdata[$i-1]);
			$delta[$i] = (6.0*$delta[$i]/($xdata[$i+1]-$xdata[$i-1])-$s*$delta[$i-1])/$p;
		}

		for( $j=$n-2; $j >= 0; --$j ) {
			$this->y2[$j] = $this->y2[$j]*$this->y2[$j+1] + $delta[$j];
		}
	}

	/**
	 * Retrieves the new data
	 *
	 * @param integer number of datapoints
	 * @return array a JLGraph data array
	 */
	function getNewData($num=50) {
		$n = $this->n ;
		$start = $this->xdata[0];
		$step = ($this->xdata[$n-1]-$start) / ($num-1);
		$data = array();
		$data[] = array('name' => $start, 'value' => $this->ydata[0]); 
		for( $j=1; $j < $num; ++$j ) {
			$xnew[$j] = $start+$j*$step;
			$ynew[$j] = $this->interpolate($xnew[$j]);
			$data[] = array('name' => $xnew[$j], 'value' => $ynew[$j]);
		}
		return $data;
	}

	/**
	 * Interpolates a new y value by given x value
	 *
	 * @param double x value
	 * @return double y value
	 */
	function interpolate($xpoint) {

		$max = $this->n-1;
		$min = 0;

		while( $max-$min > 1 ) {
			$k = ($max+$min) / 2;
			if( $this->xdata[$k] > $xpoint ){
				$max=$k;
			} else {
				$min=$k;
			}
		}

		$h = $this->xdata[$max]-$this->xdata[$min];

		if( $h == 0  ) {
			die('Invalid input data for spline. Two or more consecutive input X-values are equal.');
		}

		$a = ($this->xdata[$max] - $xpoint) / $h;
		$b = ($xpoint - $this->xdata[$min]) / $h;
		$c = ((pow($a,3) - $a) * $this->y2[$min] + (pow($b,3) - $b) * $this->y2[$max] ) * pow($h,2) / 6.0;
		return ($a * $this->ydata[$min]) + ($b * $this->ydata[$max]) + $c;
	}
}
?>