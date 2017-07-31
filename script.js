
var app = angular.module('calculatorApp', ['rzModule', 'ui.bootstrap']);

app.controller('calculatorCtrl', function ($scope, $rootScope, $timeout, $modal) {

$scope.msg = "";
$scope.chkselect = true;
$scope.state = "AK";

// Are you a US Citizen or Permanent Resident?
$scope.checkValidation = function() {
	var chkselect = $scope.chkselect;
	if (chkselect == false) { $scope.validationmsg = true; } 
	else { $scope.validationmsg = false; }
}

$scope.low = false;
$scope.mid = false;
$scope.high = false;

$scope.I = false;
$scope.II = false;
$scope.III = false;
$scope.IV = false;

// Calculate
$scope.calculate = function() {
	
	$scope.low = false;
	$scope.mid = false;
	$scope.high = false;
	
	var rawScore = ((0.64091*($scope.sliderGPA.value))-0.28946+(0.036599*($scope.sliderScore.value))-1.6)/0.019636;
	
	if (rawScore >= 34.5) {
		//$scope.msg = get_option('high_score');
		$scope.high = true;
			$scope.mid = false;
			$scope.low = false;
	} else if (rawScore >= 29.5) {
		//$scope.msg = get_option('mid_score');
		$scope.mid = true;
			$scope.high = false;
			$scope.low = false;
	} else {
		//$scope.msg = get_option('low_score');
		$scope.low = true;
			$scope.high = false;
			$scope.mid = false;
	}
}

$scope.nonresidentCalculate = function() {
	/*
	I. gpa or act too low - no scholarship
	II. wue: gpa and act in middle - wue scholarship	
	III. gpa and act high - gem scholarship
	IV. non wue: gpa and act in middle - too low for gem 
	*/
	
	$scope.I = false;
	$scope.II = false;
	$scope.III = false;
	$scope.IV = false;
	
	//gpa or act too low: no scholarship
	if ($scope.sliderGPA.value < 3.2 || $scope.sliderScore.value < 21 ) {
		$scope.I = true;
	//gpa and act high: gem scholarship
	} else if ($scope.sliderGPA.value >= 3.6 && $scope.sliderScore.value >= 26) {
		$scope.III = true;
	//gpa and act in middle
	} else { 
		if ($scope.state == 'NA' || $scope.chkselect == false) { //$scope.chkselect == false
			// non-wue: too low for gem
			$scope.IV = true;
		} else {
			// wue: wue scholarship
			$scope.II = true;
		}
	}
}

//Vertical sliders
$scope.sliderScore = {
    value: 11,
    options: {
        floor: 11,
        ceil: 36,
        vertical: true, 
        showTicksValues: true,
		step: 1,
        stepsArray: [
            {value: 11, legend: '560-620'}, 
            {value: 12, legend: '630-710'}, 
            {value: 13, legend: '720-750'}, 
            {value: 14, legend: '760-800'}, 
            {value: 15, legend: '810-850'}, 
            {value: 16, legend: '860-890'}, 
            {value: 17, legend: '900-930'}, 
            {value: 18, legend: '940-970'}, 
            {value: 19, legend: '980-1010'}, 
            {value: 20, legend: '1020-1050'}, 
            {value: 21, legend: '1060-1090'}, 
            {value: 22, legend: '1100-1120'}, 
            {value: 23, legend: '1130-1150'}, 
            {value: 24, legend: '1160-1190'}, 
            {value: 25, legend: '1200-1230'}, 
            {value: 26, legend: '1240-1270'}, 
            {value: 27, legend: '1280-1300'}, 
            {value: 28, legend: '1310-1340'}, 
           	{value: 29, legend: '1350-1380'}, 
           	{value: 30, legend: '1390-1410'}, 
           	{value: 31, legend: '1420-1440'}, 
           	{value: 32, legend: '1450-1480'}, 
           	{value: 33, legend: '1490-1510'}, 
           	{value: 34, legend: '1520-1550'}, 
           	{value: 35, legend: '1560-1590'}, 
           	{value: 36, legend: '1600'}
        ]
    }
};

//GPA Slider
$scope.sliderGPA = {
    value: 2.00,
    options: {
        floor: 2.00,
        ceil: 4.00,
		step: 0.01,
		precision: 3,
        vertical: true
    }
};
});
