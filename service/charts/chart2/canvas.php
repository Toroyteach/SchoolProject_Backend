<canvas id="my_canvas" width="600" height="500" style="border: 2px solid black;">
<script type="text/javascript">
        function BarChart(config) {  
            // user defined properties  
            this.canvas = document.getElementById(config.canvasId);  
            this.data = config.data;  
            this.color = config.color;  
            this.barWidth = config.barWidth;  
            this.gridLineIncrement = config.gridLineIncrement;  
   
            this.maxValue = config.maxValue - Math.floor(config.maxValue % this.gridLineIncrement);  
            this.minValue = config.minValue;  
   
            // constants  
            this.font = "12pt Calibri";  
            this.axisColor = "#555";  
            this.gridColor = "black";  
            this.padding = 10;  
   
            // relationships  
            this.context = this.canvas.getContext("2d");  
            this.range = this.maxValue - this.minValue;  
            this.numGridLines = this.numGridLines = Math.round(this.range / this.gridLineIncrement);  
            this.longestValueWidth = this.getLongestValueWidth();  
            this.x = this.padding + this.longestValueWidth;  
            this.y = this.padding * 2;  
            this.width = this.canvas.width - (this.longestValueWidth + this.padding * 2);  
            this.height = this.canvas.height - (this.getLabelAreaHeight() + this.padding * 4);  
   
            // draw bar chart  
            this.drawGridlines();  
            this.drawYAxis();  
            this.drawXAxis();  
            this.drawBars();  
            this.drawYVAlues();  
            this.drawXLabels();  
        }  
   
        BarChart.prototype.getLabelAreaHeight = function () {  
            this.context.font = this.font;  
            var maxLabelWidth = 0;  
   
            for (var n = 0; n < this.data.length; n++) {  
                var label = this.data[n].label;  
                maxLabelWidth = Math.max(maxLabelWidth, this.context.measureText(label).width);  
            }  
   
            return Math.round(maxLabelWidth / Math.sqrt(2));  
        };  
   
        BarChart.prototype.getLongestValueWidth = function () {  
            this.context.font = this.font;  
            var longestValueWidth = 0;  
            for (var n = 0; n <= this.numGridLines; n++) {  
                var value = this.maxValue - (n * this.gridLineIncrement);  
                longestValueWidth = Math.max(longestValueWidth, this.context.measureText(value).width);  
   
            }  
            return longestValueWidth;  
        };  
   
        BarChart.prototype.drawXLabels = function () {  
            var context = this.context;  
            context.save();  
            var data = this.data;  
            var barSpacing = this.width / data.length;  
   
            for (var n = 0; n < data.length; n++) {  
                var label = data[n].label;  
                context.save();  
                context.translate(this.x + ((n + 1 / 2) * barSpacing), this.y + this.height + 10);  
                context.rotate(-1 * Math.PI / 4); // rotate 45 degrees  
                context.font = this.font;  
                context.fillStyle = "black";  
                context.textAlign = "right";  
                context.textBaseline = "middle";  
                context.fillText(label, 0, 0);  
                context.restore();  
            }  
            context.restore();  
        };  
   
        BarChart.prototype.drawYVAlues = function () {  
            var context = this.context;  
            context.save();  
            context.font = this.font;  
            context.fillStyle = "black";  
            context.textAlign = "right";  
            context.textBaseline = "middle";  
   
            for (var n = 0; n <= this.numGridLines; n++) {  
                var value = this.maxValue - (n * this.gridLineIncrement);  
                var thisY = (n * this.height / this.numGridLines) + this.y;  
                context.fillText(value, this.x - 5, thisY);  
            }  
   
            context.restore();  
        };  
   
        BarChart.prototype.drawBars = function () {  
            var context = this.context;  
            context.save();  
            var data = this.data;  
            var barSpacing = this.width / data.length;  
            var unitHeight = this.height / this.range;  
   
            for (var n = 0; n < data.length; n++) {  
                var bar = data[n];  
                var barHeight = (data[n].value - this.minValue) * unitHeight;  
   
                if (barHeight > 0) {  
                    context.save();  
                    context.translate(Math.round(this.x + ((n + 1 / 2) * barSpacing)), Math.round(this.y + this.height));  
                     
                    context.scale(1, -1);  
   
                    context.beginPath();  
                    context.rect(-this.barWidth / 2, 0, this.barWidth, barHeight);  
                    context.fillStyle = this.color;  
                    context.fill();  
                    context.restore();  
                }  
            }  
            context.restore();  
        };  
   
        BarChart.prototype.drawGridlines = function () {  
            var context = this.context;  
            context.save();  
            context.strokeStyle = this.gridColor;  
            context.lineWidth = 2;  
   
            // draw y axis grid lines  
            for (var n = 0; n < this.numGridLines; n++) {  
                var y = (n * this.height / this.numGridLines) + this.y;  
                context.beginPath();  
                context.moveTo(this.x, y);  
                context.lineTo(this.x + this.width, y);  
                context.stroke();  
            }  
            context.restore();  
        };  
   
        BarChart.prototype.drawXAxis = function () {  
            var context = this.context;  
            context.save();  
            context.beginPath();  
            context.moveTo(this.x, this.y + this.height);  
            context.lineTo(this.x + this.width, this.y + this.height);  
            context.strokeStyle = this.axisColor;  
            context.lineWidth = 2;  
            context.stroke();  
            context.restore();  
        };  
   
        BarChart.prototype.drawYAxis = function () {  
            var context = this.context;  
            context.save();  
            context.beginPath();  
            context.moveTo(this.x, this.y);  
            context.lineTo(this.x, this.height + this.y);  
            context.strokeStyle = this.axisColor;  
            context.lineWidth = 2;  
            context.stroke();  
            context.restore();  
        };  
   
        window.onload = function () {  
            var data = [{  
                label: "Eating",  
                value: 2  
            }, {  
                label: "Working",  
                value: 8  
            }, {  
                label: "Sleeping",  
                value: 8  
            }, {  
                label: "Playing",  
                value: 2  
            }, {  
                label: "Entertainment",  
                value: 4  
            }];  
   
            new BarChart({  
                canvasId: "my_canvas",  
                data: data,  
                color: "red",  
                barWidth: 50,  
                minValue: 0,  
                maxValue: 10,  
                gridLineIncrement: 2  
            });  
        }; 
</script>