<?php
// +----------------------------------------------------------------------
// | 平面几何类
// +----------------------------------------------------------------------

/*
//点坐标
$point = array(80,123);
//多边形坐标
$polygon = array(
	array(80, 123),
	array(210, 98),
	array(265, 182),
	array(218, 214),
	array(185, 149),
	array(131, 160),
	array(158, 225),
	array(88, 230),
);
//判断点是否在多边形内
$res = helper_polygon::isPointInPolygon($point,$polygon);
*/

class helper_polygon {

	/**
     * 判断点是否在多边形内
     * @param array $point 点坐标
     * @param array $polygon 多边形坐标
     * @return bool 点在多边形内返回true,否则返回false
     */
	static public function isPointInPolygon($point, $polygon){
		$bounds = self::getPolygonMaxBox($polygon);

		//首先判断点是否在多边形的外包矩形内，如果在，则进一步判断，否则返回false
		if(!self::isPointInRect($point,$bounds)){
			return false;
		}

		$pts = $polygon;
		$N = count($pts);
        $boundOrVertex = true; //如果点位于多边形的顶点或边上，也算做点在多边形内，直接返回true
        $intersectCount = 0;//cross points count of x
        $precision = 2e-10; //浮点类型计算时候与0比较时候的容差

        $p = $point; //测试点
		$p1 = $pts[0];

		for($i = 1; $i <= $N; ++$i){//check all rays
			if($p[0]==$p1[0]&&$p[1]==$p1[1]){
                return $boundOrVertex;//p is an vertex
            }

			$p2 = $pts[$i % $N];//right vertex
			if($p[1] < min($p1[1], $p2[1]) || $p[1] > max($p1[1], $p2[1])){//ray is outside of our interests
                $p1 = $p2;
                continue;//next ray left point
            }

			if($p[1] > min($p1[1], $p2[1]) && $p[1] < max($p1[1], $p2[1])){//ray is crossing over by the algorithm (common part of)
                if($p[0] <= max($p1[0], $p2[0])){//x is before of ray
                    if($p1[1] == $p2[1] && $p[0] >= min($p1[0], $p2[0])){//overlies on a horizontal ray
                        return $boundOrVertex;
                    }

                    if($p1[0] == $p2[0]){//ray is vertical
                        if($p1[0] == $p[0]){//overlies on a vertical ray
                            return $boundOrVertex;
                        }else{//before ray
                            ++$intersectCount;
                        }
                    }else{//cross point on the left side
                        $xinters = ($p[1] - $p1[1]) * ($p2[0] - $p1[0]) / ($p2[1] - $p1[1]) + $p1[0];//cross point of lng
                        if(abs($p[0] - $xinters) < $precision){//overlies on a ray
                            return $boundOrVertex;
                        }

                        if($p[0] < $xinters){//before ray
                            ++$intersectCount;
                        }
                    }
                }
            }else{//special case when ray is crossing through the vertex
                if($p[1] == $p2[1] && $p[0] <= $p2[0]){//p crossing over p2
                    $p3 = $pts[($i+1) % $N]; //next vertex
                    if($p[1] >= min($p1[1], $p3[1]) && $p[1] <= max($p1[1], $p3[1])){//$p[1] lies between $p1[1] & p3.lat
                        ++$intersectCount;
                    }else{
                        $intersectCount += 2;
                    }
                }
            }
            $p1 = $p2;//next ray left point
		}

		if($intersectCount % 2 == 0){//偶数在多边形外
            return false;
        } else { //奇数在多边形内
            return true;
        }

	}

	/**
     * 判断点是否在矩形内
     * @param array $point 点坐标
     * @param array $bounds 矩形点坐标
     * @return bool 点在矩形内返回true,否则返回false
     */
	static public function isPointInRect($point, $bounds){
		$sw = $bounds['sw']; //西南脚点
        $ne = $bounds['ne']; //东北脚点
        return ($point[0] >= $sw[0] && $point[0] <= $ne[0] && $point[1] >= $sw[1] && $point[1] <= $ne[1]);
	}

	/**
     * 获取多边形的外包矩形最大和最小坐标
     * @param array $polygon 多边形坐标
     * @return bool 点在矩形内返回true,否则返回false
     */
	static public function getPolygonMaxBox($polygon) {
		$min = $max = $polygon[0];
		foreach ($polygon as $v) {
			if ($v[0] < $min[0]) {
				$min[0] = $v[0];
			}
			if ($v[1] < $min[1]) {
				$min[1] = $v[1];
			}
			if ($v[0] > $max[0]) {
				$max[0] = $v[0];
			}
			if ($v[1] > $max[1]) {
				$max[1] = $v[1];
			}
		}
		//sw:西南脚点  ne:东北脚点
		return array('sw'=>$min, 'ne'=>$max);
	}
}