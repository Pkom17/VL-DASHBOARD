$(function () {
    $('#requests').highcharts({
        title: {
            text: '',
        },
        subtitle: {
            text: '',
            x: -20
        },
        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        yAxis: {
            title: {
                text: ''
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        legend: {
            enabled: false
        },
        series: [{
           data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6]
        }]
    });
});
$(function () {
    $('#sampleEntry').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["Entered at site","Entered at Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							57484
        						]
        			}
        		]
        });
});
$(function () {
    $('#sampleEntryVsReceived').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["Entered received same day","Entered not received same day"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							57484
        						]
        			}
        		]
        });
});
$(function () {
    $('#siteEntryLab').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							3953,
        							41368,
        							25523,
        							15644,
        							64303,
        							37885
        						]
        			}
        		]
        });
});
$(function () {
    $('#receivedSampleLab').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							3953,
        							41368,
        							25523,
        							15644,
        							64303,
        							37885
        						]
        			}
        		]
        });
});
$(function () {
    $('#inqueueLabs').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							3953,
        							41368,
        							25523,
        							15644,
        							64303,
        							37885
        						]
        			}
        		]
        });
});
$(function () {
    $('#inprocessLabs').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							3953,
        							41368,
        							25523,
        							15644,
        							64303,
        							37885
        						]
        			}
        		]
        });
});
$(function () {
    $('#processedSamples').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							3953,
        							41368,
        							25523,
        							15644,
        							64303,
        							37885
        						]
        			}
        		]
        });
});
$(function () {
    $('#pendsApproval').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							3953,
        							41368,
        							25523,
        							15644,
        							64303,
        							37885
        						]
        			}
        		]
        });
});

$(function () {
    $('#dispatchedResults').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							3953,
        							41368,
        							25523,
        							15644,
        							64303,
        							37885
        						]
        			}
        		]
        });
});
$(function () {
    $('#oldestSamples').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["KEMRI Nairobi","Kisumu Lab","Kericho Lab","Eldoret Lab","Coast Lab","NHRL Nairobi","Nyumbani Lab"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Days'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							7,
        							6,
        							5,
        							4,
        							3,
        							3,
        							4
        						]
        			}
        		]
        });
});

$(function () {
    $('#inprocessPlatform').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["Abbott","Roche","Panther"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							39503,
        							41368
        						]
        			}
        		]
        });
});
$(function () {
    $('#processedPlatform').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: ''
        },
        xAxis: {
            categories: ["Abbott","Roche","Panther"]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Samples'
            },
            stackLabels: {
            	rotation: -75,
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                },
                y:-20
            }
        },
        legend: {
        	enabled: false
        },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>% contribution: {point.percentage:.1f}%'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                    style: {
                        textShadow: '0 0 3px black'
                    }
                }
            }
        },colors: [
	        '#1BA39C'
	    ],
        series: [
        			{
        				"data": [
        							41368,
        							39503,
        							41368
        						]
        			}
        		]
        });
});