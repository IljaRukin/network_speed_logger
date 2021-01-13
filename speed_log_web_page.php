<!DOCTYPE html>
<meta charset="utf-8">
<html>
<head>
<title>network speed logger</title>
<style>
    body {
        width: 80%;
        margin: 0 auto;
        font-family: Tahoma, Verdana, Arial, sans-serif;
  background:#444;
  color:#ddd;
    }
</style>

<script src="https://d3js.org/d3.v4.js"></script>
</head>
<body>
<h1>internet speed monitor</h1>
<p>This site displays the speed of our network.<br>
The logging is done by running speedtest-sli every hour at 3 minutes past each hour.<br>
The upload and download  speed is given in bit/s and the ping in ms.</p>

<h2>speed (in bit/s)</h2>
<div id="speed"></div>

<h2>ping (in ms)</h2>
<div id="ping"></div>

<script>

// parse the date / time
var parseTime = d3.timeParse("%Y-%m-%dT%H:%M:%S.%L%LZ");

//##### speed plot setup #####

// set the dimensions and margins of the graph
var margin = {top: 10, right: 30, bottom: 30, left: 60},
    width1 = 800 - margin.left - margin.right,
    height1 = 300 - margin.top - margin.bottom;

// set the ranges
var x = d3.scaleTime().range([0, width1]);
var y = d3.scaleLinear().range([height1, 0]);

// define the 1st line
var valueline = d3.line()
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.download); });

// define the 2nd line
var valueline2 = d3.line()
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.upload); });


// append the svg obgect to the the page
var svg1 = d3.select("#speed")
  .append("svg")
    .attr("width", width1 + margin.left + margin.right)
    .attr("height", height1 + margin.top + margin.bottom)
  .append("g")
    .attr("transform",
          "translate(" + margin.left + "," + margin.top + ")");

//##### ping plot setup #####

// set the dimensions and margins of the graph
var margin = {top: 10, right: 30, bottom: 30, left: 60},
    width2 = 800 - margin.left - margin.right,
    height2 = 400 - margin.top - margin.bottom;

// set the ranges
var xx = d3.scaleTime().range([0, width2]);
var yy = d3.scaleLinear().range([height2, 0]);

// define the 1st line
var latencyline = d3.line()
    .x(function(d) { return xx(d.date); })
    .y(function(d) { return yy(d.ping); });

// append the svg obgect to the the page
var svg2 = d3.select("#ping")
  .append("svg")
    .attr("width", width2 + margin.left + margin.right)
    .attr("height", height2 + margin.top + margin.bottom)
  .append("g")
    .attr("transform",
          "translate(" + margin.left + "," + margin.top + ")");


//##### Get the data #####

d3.csv("net_speed/speed_log.csv", function(error, data) {
  if (error) throw error;

  // format the data
//Server ID,Sponsor,Server Name,Timestamp,Distance,Ping,Download,Upload,Share,IP Address
  data.forEach(function(d) {
      d.server_id = +d.Server_ID;
      d.sponsor = +d.Sponsor;
      d.server_name = +d.Server_Name;
      d.date = parseTime(d.Timestamp);
      d.distance = +d.Distance;
      d.ping = +d.Ping;
      d.download = +d.Download;
      d.upload = +d.Upload;
      d.share = +d.Share;
      d.ip_adress = +d.IP_Address;
  });

//##### plot speed #####

  // Scale the range of the data
  x.domain(d3.extent(data, function(d) { return d.date; }));
  y.domain([0, d3.max(data, function(d) {
    return Math.max(d.download); })]);

  // Add the valueline path.
  svg1.append("path")
      .data([data])
      .attr("fill", "none")
      .attr("class", "line")
      .style("stroke", "green")
      .attr("d", valueline);

  // Add the valueline2 path.
  svg1.append("path")
      .data([data])
      .attr("fill", "none")
      .attr("class", "line")
      .style("stroke", "blue")
      .attr("d", valueline2);

  // Add the X Axis
  svg1.append("g")
      .attr("transform", "translate(0," + height1 + ")")
      .call(d3.axisBottom(x));

  // Add the Y Axis
  svg1.append("g")
      .call(d3.axisLeft(y));

  //legend
  svg1.append("circle").attr("cx",600).attr("cy",30).attr("r", 5).style("fill", "green")
  svg1.append("circle").attr("cx",600).attr("cy",60).attr("r", 5).style("fill", "blue")
  svg1.append("text").attr("x", 620).attr("y", 30).text("download").style("font-size", "12px").attr("alignment-baseline","middle")
  svg1.append("text").attr("x", 620).attr("y", 60).text("upload").style("font-size", "12px").attr("alignment-baseline","middle")

//##### plot latency #####

  // Scale the range of the data
  xx.domain(d3.extent(data, function(d) { return d.date; }));
  yy.domain([0, d3.max(data, function(d) {
    return Math.max(d.ping); })]);

  // Add the valueline path.
  svg2.append("path")
      .data([data])
      .attr("class", "line")
      .style("stroke", "blue")
      .attr("fill", "none")
      .attr("d", latencyline);

  // Add the X Axis
  svg2.append("g")
      .attr("transform", "translate(0," + height2 + ")")
      .call(d3.axisBottom(xx));

  // Add the Y Axis
  svg2.append("g")
      .call(d3.axisLeft(yy));

  //legend
  svg2.append("circle").attr("cx",600).attr("cy",30).attr("r", 5).style("fill", "blue")
  svg2.append("text").attr("x", 620).attr("y", 30).text("ping").style("font-size", "12px").attr("alignment-baseline","middle")

});

</script>


</body>
</html>
