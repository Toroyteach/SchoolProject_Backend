<?php
require('top.inc.php');

isAdmin();

//Queries to get data for Graphs
// Query to retrieve data
$sql = "SELECT p.option_name, COUNT(u.user_id) AS count FROM tbl_user_push_preferences u JOIN tbl_push_options p ON u.option_id = p.option_id GROUP BY p.option_name;";
$result = $con->query($sql);
// Array to store data
while ($row = $result->fetch_assoc()) {
    $data[$row['option_name']] = (int)$row['count'];
}
// Convert data array to JSON
$json_data = json_encode($data);


// Query to retrieve counts from tables
$sqlUsers = "SELECT COUNT(*) AS user_count FROM tbl_member";
$sqlNotifications = "SELECT COUNT(*) AS notification_count FROM tbl_user_push_preferences";
$sqlItems = "SELECT COUNT(*) AS item_count FROM tbl_push_options";

// Fetch user count
$resultUsers = $con->query($sqlUsers);
$rowUsers = $resultUsers->fetch_assoc();
$userCount = (int)$rowUsers['user_count'];

// Fetch notification count
$resultNotifications = $con->query($sqlNotifications);
$rowNotifications = $resultNotifications->fetch_assoc();
$notificationCount = (int)$rowNotifications['notification_count'];

// Fetch item count
$resultItems = $con->query($sqlItems);
$rowItems = $resultItems->fetch_assoc();
$itemCount = (int)$rowItems['item_count'];

// Close connection
$con->close();

echo "<script>var chartData = $json_data;</script>";
?>
<div class="content pb-0">
	<div class="orders">
		<div class="row">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-body">
						<h4 class="box-title">DASHBOARD </h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="container">
	<div class="row">
		<div class="col-6">
			<h3>Users Option Choices</h3>
			<canvas id="myCanvas"></canvas>
			<div for="myCanvas"></div>
		</div>
		<div class="col-6">
		<div class="container">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Members Registered</h5>
                <p class="card-text"><?php echo $userCount; ?></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Members Notification Selected</h5>
                <p class="card-text"><?php echo $notificationCount; ?></p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Push Notifications Options</h5>
                <p class="card-text"><?php echo $itemCount; ?></p>
            </div>
        </div>
    </div>

		</div>
	</div>
</div>

<script type="text/javascript">
    var myCanvas = document.getElementById("myCanvas");
    myCanvas.width = 500;
    myCanvas.height = 340;

    var ctx = myCanvas.getContext("2d");

    function drawLine(ctx, startX, startY, endX, endY, color) {
        ctx.save();
        ctx.strokeStyle = color;
        ctx.beginPath();
        ctx.moveTo(startX, startY);
        ctx.lineTo(endX, endY);
        ctx.stroke();
        ctx.restore();
    }

    function drawArc(ctx, centerX, centerY, radius, startAngle, endAngle, color) {
        ctx.save();
        ctx.strokeStyle = color;
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.stroke();
        ctx.restore();
    }

    function drawPieSlice( ctx, centerX, centerY, radius, startAngle, endAngle, fillColor, strokeColor) {
        ctx.save();
        ctx.fillStyle = fillColor;
        ctx.strokeStyle = strokeColor;
        ctx.beginPath();
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, startAngle, endAngle);
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
        ctx.restore();
    }

    class PieChart {
        constructor(options) {
            this.options = options;
            this.canvas = options.canvas;
            this.ctx = this.canvas.getContext("2d");
            this.colors = options.colors;
            this.titleOptions = options.titleOptions;
            this.totalValue = [...Object.values(this.options.data)].reduce(
                (a, b) => a + b,
                0
            );
            this.radius =
                Math.min(this.canvas.width / 2, this.canvas.height / 2) - options.padding;
        }

        drawSlices() {
            var colorIndex = 0;
            var startAngle = -Math.PI / 2;

            for (var categ in this.options.data) {
                var val = this.options.data[categ];
                var sliceAngle = (2 * Math.PI * val) / this.totalValue;

                drawPieSlice(
                    this.ctx,
                    this.canvas.width / 2,
                    this.canvas.height / 2,
                    this.radius,
                    startAngle,
                    startAngle + sliceAngle,
                    this.colors[colorIndex % this.colors.length]
                );

                startAngle += sliceAngle;
                colorIndex++;
            }

            if (this.options.doughnutHoleSize) {
                drawPieSlice(
                    this.ctx,
                    this.canvas.width / 2,
                    this.canvas.height / 2,
                    this.options.doughnutHoleSize * this.radius,
                    0,
                    2 * Math.PI,
                    "#FFF",
                    "#FFF"
                );

                drawArc(
                    this.ctx,
                    this.canvas.width / 2,
                    this.canvas.height / 2,
                    this.options.doughnutHoleSize * this.radius,
                    0,
                    2 * Math.PI,
                    "#000"
                );
            }
        }

        drawLabels() {
            var colorIndex = 0;
            var startAngle = -Math.PI / 2;
            for (var categ in this.options.data) {
                var val = this.options.data[categ];
                var sliceAngle = (2 * Math.PI * val) / this.totalValue;
                var labelX = this.canvas.width / 2 + (this.radius / 2) * Math.cos(startAngle + sliceAngle / 2);
                var labelY =
                    this.canvas.height / 2 +
                    (this.radius / 2) * Math.sin(startAngle + sliceAngle / 2);

                if (this.options.doughnutHoleSize) {
                    var offset = (this.radius * this.options.doughnutHoleSize) / 2;
                    labelX =
                        this.canvas.width / 2 +
                        (offset + this.radius / 2) * Math.cos(startAngle + sliceAngle / 2);
                    labelY =
                        this.canvas.height / 2 +
                        (offset + this.radius / 2) * Math.sin(startAngle + sliceAngle / 2);
                }

                var labelText = Math.round((100 * val) / this.totalValue);
                this.ctx.fillStyle = "black";
                this.ctx.font = "32px Khand";
                this.ctx.fillText(labelText + "%", labelX, labelY);
                startAngle += sliceAngle;
            }
        }

        drawTitle() {
            this.ctx.save();

            this.ctx.textBaseline = "bottom";
            this.ctx.textAlign = this.titleOptions.align;
            this.ctx.fillStyle = this.titleOptions.fill;
            this.ctx.font = `${this.titleOptions.font.weight} ${this.titleOptions.font.size} ${this.titleOptions.font.family}`;

            let xPos = this.canvas.width / 2;

            if (this.titleOptions.align == "left") {
                xPos = 10;
            }
            if (this.titleOptions.align == "right") {
                xPos = this.canvas.width - 10;
            }

            this.ctx.fillText(this.options.seriesName, xPos, this.canvas.height);

            this.ctx.restore();
        }

        drawLegend() {
            let pIndex = 0;
            let legend = document.querySelector("div[for='myCanvas']");
            let ul = document.createElement("ul");
            legend.append(ul);

            for (let ctg of Object.keys(this.options.data)) {
                let li = document.createElement("li");
                li.style.listStyle = "none";
                li.style.borderLeft =
                    "20px solid " + this.colors[pIndex % this.colors.length];
                li.style.padding = "5px";
                li.textContent = ctg;
                ul.append(li);
                pIndex++;
            }
        }

        draw() {
            this.drawSlices();
            this.drawLabels();
            this.drawTitle();
            this.drawLegend();
        }
    }

	//Feed the object with data from database
    var myPiechart = new PieChart({
        canvas: myCanvas,
        seriesName: "Options Ratio",
        padding: 40,
        data: chartData,
        colors: ["#80DEEA", "#FFE082", "#FFAB91", "#CE93D8"],
        titleOptions: {
            align: "center",
            fill: "black",
            font: {
                weight: "bold",
                size: "18px",
                family: "Lato"
            }
        }
    });

    myPiechart.draw();
</script>

<?php
require('footer.inc.php');
?>