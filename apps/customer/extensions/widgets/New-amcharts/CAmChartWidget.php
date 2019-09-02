<?php

/**
 * Copyright (c) 2010 Lucas Gómez <luckas1984@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 *
 * This widget for use with the Yii Framework utilises the Amchart plugin visualize
 * (http://www.amcharts.com/) to render graphs and
 * charts for your web application.
 *
 * For information on istallation and useage please visit the porjects hosting page
 * on google code: http://code.google.com/p/yii-amchart-widget/
 */

class CAmchartWidget extends CWidget
{	
	/**
	 * @var String
	 * Width of Chart
	 */
	public $width=400;
	
	/**
	 * @var string
	 * Height of Chart
	 */
	public $height=400;
	
	
	/**
	 * @var array
	 * Collection of option to customize the chart
	 */
	public $Chart = array();
	
	
	/**
	 * @var array
	 * Collection of Charts that will be displayed on Chart
	 */
	public $Graphs = array();
	
	
	/**
	 * @var array
	 * Collection of option to customize the Cateogry Axis
	 */
	public $CategoryAxis = array();
	
	/**
	 * @var array
	 * Collection of option to customize the Value Axis
	 */
	public $ValueAxis = array();
	
	/**
	* @var array 
	* Valid chart types
	*/
	private $_validChartTypes = array('column','bar','line','area','pie');
	
	
	/**
	 * @var array
	 * AmChart Options
	 */
	private $_defaultsAmChartOptions = array(
				'fontFamily'=>'Arial,Helvetica, Sans',
				'startDuration'=>'1',
				'dataProvider'=>array(),
				'categoryField'=> ''
			);
	
	
	/**
	 * @var array
	 * AmPieChart Options
	 */
	private $_defaultsAmPieChartOptions = array(
			'fontFamily'=>'Arial,Helvetica, Sans',
			'startDuration'=>'1',
			'dataProvider'=>array(),
			'sequencedAnimation' => true,
			'startEffect' => 'elastic',
			'innerRadius' => '30%',
			'startDuration' => 2,
			'labelRadius' => 10,
			'radius' => '45%',
			'pullOutRadius' => 1,
	);

	
	/**
	 * @var array
	 * AmGraph Options
	 */
	private $_defaultsAmGraphOptions = array(
			'title'=>'',
			'lineColor'=>'#CCCCCC',
			'lineAlpha'=>'1',
			'lineThickness' => '1',
			'fillColors'=>'#CCCCCC',
			'fillAlphas'=>'1',
			'valueField'=> '',
			'type'=>'column'
	);
	
	
	/**
	 * @var array
	 * CategoryAxis Options
	 */
	private $_defaultsCategoryAxisOptions = array(
			'title'=>'',
			'gridPosition'=>'start',
			'axisAlpha'=>0,
			'dashLength'=>1
	);
	
	
	/**
	 * @var array
	 * ValueAxis Options
	 */
	private $_defaultsValueAxisOptions = array(
			'title'=>'',
			'axisAlpha'=>0,
			'dashLength'=>1
	);
	
	
    /**
     * @var int
     */
    private static $count = 0;
	
	
	/**
	 * The initialisation method
	 */
	public function init()
	{
        
		// ensure valid chart type selected
		foreach ($this->Graphs as $graph)
			if(!in_array($graph['type'], $this->_validChartTypes))
				throw new CException($graph['type'] . ' is an invalid chart type. Valid charts are ' . implode(',',$this->_validChartTypes));
		
		// check dataProvider is present
		if(empty($this->Chart['dataProvider']))
			throw new CException('Please provide some dataProvider to render a display');
		
		$this->_registerWidgetScripts();
		
		parent::init();
	}
	
	
	/**
	 * registerCoreScripts
	 */
	private function _registerWidgetScripts()
	{
		$cs=Yii::app()->getClientScript();
		//$cs->registerCoreScript('jquery');
		
		$basePath = Yii::getPathOfAlias('customer.extensions.widgets.amcharts.assets');
		$baseUrl = Yii::app()->getAssetManager()->publish($basePath);
		
		$cs->registerScriptFile($baseUrl.'/amcharts.js');
	}
	
	/*
	 * Array Obfuscation
	 * 
	 */
	private static function renameKeys(&$array, $replacement_keys)
	{
		$array = array_combine(array_keys($array), $replacement_keys);
	}
	
	/**
	 * Render the output
	 */
	public function run()
	{	
		$newArray = array();
		foreach ($this->Chart['dataProvider'] as $modelData)
			$newArray[] = $modelData->attributes;
		
			
		//foreach ($this->Graphs as $graph)
			//$graph['valueField'] = md5($graph['valueField']);
		
		$this->Chart['dataProvider'] = json_encode($newArray);
		
		
		foreach ($this->Graphs as &$graph)
			$graph = array_merge($this->_defaultsAmGraphOptions, $graph);
			
		
		if(isset($this->Chart['titleField']))
			$this->render('visualizePie',
					array(
							'chart'=>array_merge($this->_defaultsAmPieChartOptions, $this->Chart),
							'width'=>$this->width,
							'height'=>$this->height
					),
					false
			);
		else
			$this->render('visualizeSerial',
						array(
						'chart'=>array_merge($this->_defaultsAmChartOptions, $this->Chart),
						'graphs'=>$this->Graphs,
						'categoryAxis'=>array_merge($this->_defaultsCategoryAxisOptions, $this->CategoryAxis),
						'valueAxis'=>array_merge($this->_defaultsAmGraphOptions, $this->ValueAxis),
						'width'=>$this->width,
						'height'=>$this->height
						),
						false
					);
	}
	
}


?>
