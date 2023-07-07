<?php
class UserCropDistribution
{
    // Connection
    private $conn;

    // Db connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createCrops($crop_name, $planting_date, $harvesting_date, $user_id)
    {

        $stmt = $this->conn->prepare("INSERT INTO tbl_crops (crop_name, planting_date, harvesting_date, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $crop_name, $planting_date, $harvesting_date, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function updateCrops($crop_id, $crop_name, $planting_date, $harvesting_date)
    {

        $sqlQuery = "UPDATE tbl_crops SET crop_name = ?, planting_date = ?, harvesting_date = ?, WHERE crop_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("sssi", $crop_name, $planting_date, $harvesting_date, $crop_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function deleteCrops($crop_id)
    {

        $stmt = $this->conn->prepare("DELETE FROM tbl_crops WHERE crop_id = ?");
        $stmt->bind_param("i", $crop_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function createSeason($season_name, $start_date, $end_date, $user_id)
    {

        $stmt = $this->conn->prepare("INSERT INTO tbl_seasons (season_name, start_date, end_date, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $season_name, $start_date, $end_date, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function updateSeason($season_name, $start_date, $end_date, $user_id)
    {

        $sqlQuery = "UPDATE tbl_season SET season_name = ?, start_date = ?, end_date = ?, WHERE user_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("sssi", $season_name, $start_date, $end_date, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function deleteSeason($season_id)
    {

        $stmt = $this->conn->prepare("DELETE FROM tbl_seasons WHERE season_id = ?");
        $stmt->bind_param("i", $season_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function createDistribution($crop_choice, $season_choice, $quantity, $user_id)
    {
        $season_id = null;
        $end_date  = null;
        $start_date = null;
        $season_name = null;

        $sqlQuery = "SELECT * FROM tbl_seasons WHERE season_name = ? AND user_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("si", $season_choice, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {

            $stmt->bind_result($season_id, $season_name, $start_date, $end_date, $user_id);
            $stmt->fetch();

            $sqlQuery = "SELECT * FROM tbl_crops WHERE crop_name = ? AND user_id = ?";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->bind_param("si", $crop_choice, $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $crop_id = null;
                $crop_name  = null;
                $planting_date = null;
                $harvesting_date = null;

                $stmt->bind_result($crop_id, $crop_name, $planting_date, $harvesting_date, $user_id);
                $stmt->fetch();


                $stmt = $this->conn->prepare("INSERT INTO tbl_distribution (crop_id, season_id, user_id, quantity) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiii", $crop_id, $season_id, $user_id, $quantity);

                if ($stmt->execute()) {
                    $stmt->close();
                    return true;
                } else {
                    $stmt->close();
                    return false;
                }
            }
        }
    }

    public function updateDistribution($crop_id, $season_id, $quantity, $user_id)
    {

        $sqlQuery = "UPDATE tbl_distribution SET crop_id = ?, season_id = ?, quantity = ?, WHERE user_id = ?";
        $stmt = $this->conn->prepare($sqlQuery);
        $stmt->bind_param("iiii", $crop_id, $season_id, $quantity, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function deleteDistribution($distribution_id)
    {

        $stmt = $this->conn->prepare("DELETE FROM tbl_distribution WHERE distribution_id = ?");
        $stmt->bind_param("i", $distribution_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function generateBarChart($user_id)
    {

        $stmt = $this->conn->prepare("SELECT season_name, quantity FROM tbl_distribution
        INNER JOIN tbl_crops ON tbl_distribution.crop_id = tbl_crops.crop_id
        INNER JOIN tbl_seasons ON tbl_distribution.season_id = tbl_seasons.season_id
        WHERE tbl_crops.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'season_name' => $row['season_name'],
                'quantity' => $row['quantity']
            );
        }

        return $data;
    }

    public function generatePieChart($user_id)
    {

        $stmt = $this->conn->prepare("SELECT crop_name, SUM(quantity) AS total_quantity FROM tbl_distribution
        INNER JOIN tbl_crops ON tbl_distribution.crop_id = tbl_crops.crop_id
        WHERE tbl_crops.user_id = ?
        GROUP BY crop_name");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'crop_name' => $row['crop_name'],
                'total_quantity' => $row['total_quantity']
            );
        }

        return $data;
    }

    public function generateLineChart($user_id)
    {

        $stmt = $this->conn->prepare("SELECT season_name, quantity FROM tbl_distribution
        INNER JOIN tbl_crops ON tbl_distribution.crop_id = tbl_crops.crop_id
        INNER JOIN tbl_seasons ON tbl_distribution.season_id = tbl_seasons.season_id
        WHERE tbl_crops.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'season_name' => $row['season_name'],
                'quantity' => $row['quantity']
            );
        }

        return $data;
    }

    public function createExpense($crop_id, $expense_date, $expense_description, $amount, $user_id)
    {

        $stmt = $this->conn->prepare("INSERT INTO tbl_expenses (crop_id, expense_date, expense_description, amount, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issdi", $crop_id, $expense_date, $expense_description, $amount, $user_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function deleteExpense($expense_id)
    {

        $stmt = $this->conn->prepare("DELETE FROM tbl_expenses WHERE expense_id = ?");
        $stmt->bind_param("i", $expense_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }

    public function getExpenseDistributionByCrop($user_id)
    {
        $query = " SELECT c.crop_name, SUM(e.amount) AS total_expenses
          FROM tbl_expenses AS e
          INNER JOIN tbl_crops AS c ON e.crop_id = c.crop_id
          WHERE c.user_id = ?
          GROUP BY c.crop_name
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'crop_name' => $row['crop_name'],
                'total_quantity' => $row['total_expenses']
            );
        }

        return $data;
    }

    public function getExpenseDistributionBySeason($user_id)
    {
        $query = " SELECT s.season_name, SUM(e.amount) AS total_expenses
          FROM tbl_expenses AS e
          INNER JOIN tbl_crops AS c ON e.crop_id = c.crop_id
          INNER JOIN tbl_seasons AS s ON c.season_id = s.season_id
          WHERE c.user_id = ?
          GROUP BY s.season_name
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'crop_name' => $row['crop_name'],
                'total_quantity' => $row['total_expenses']
            );
        }

        return $data;
    }

    public function getExpenseTrendOverTime($user_id)
    {
        $query = " SELECT YEAR(e.expense_date) AS year, MONTH(e.expense_date) AS month, SUM(e.amount) AS total_expenses
          FROM tbl_expenses AS e
          INNER JOIN tbl_crops AS c ON e.crop_id = c.crop_id
          WHERE c.user_id = ?
          GROUP BY YEAR(e.expense_date), MONTH(e.expense_date)
          ORDER BY YEAR(e.expense_date), MONTH(e.expense_date)
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'month' => $row['month'],
                'total_quantity' => $row['total_expenses']
            );
        }

        return $data;
    }

    public function getUserCrops($user_id)
    {
        $query = "SELECT * FROM tbl_crops WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'id' => $row['crop_id'],
                'crop_name' => $row['crop_name'],
                'planting_date' => $row['planting_date'],
                'harvesting_date' => $row['harvesting_date'],
                'user_id' => $row['user_id'],
            );
        }

        $response = array(
            "data" => $data,
            "status" => true,
        );

        return $response;
    }

    public function getUserSeason($user_id)
    {

        $query = "SELECT * FROM tbl_seasons WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'id' => $row['season_id'],
                'season_name' => $row['season_name'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'user_id' => $row['user_id'],
            );
        }

        $response = array(
            "data" => $data,
            "status" => true,
        );

        return $response;
    }

    public function getUserDistribution($user_id)
    {

        $query = "SELECT tbl_distribution.*, tbl_crops.crop_name, tbl_seasons.season_name FROM tbl_distribution JOIN tbl_crops ON tbl_crops.crop_id = tbl_distribution.crop_id JOIN tbl_seasons ON tbl_seasons.season_id = tbl_distribution.season_id WHERE tbl_distribution.user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'id' => $row['distribution_id'],
                'crop_name' => $row['crop_name'],
                'season_name' => $row['season_name'],
                'size' => $row['quantity'],
                'user_id' => $row['user_id'],
            );
        }

        $response = array(
            "data" => $data,
            "status" => true,
        );

        return $response;
    }

    public function getUserDashboardDataArray($user_id)
    {

        $userDashboardData = array();

        //BAR CHART DISTRIBUTION QUANTITY OF CROPS PER SEASON
        $stmt = $this->conn->prepare("SELECT season_name, SUM(quantity) AS total_quantity FROM tbl_seasons
        JOIN tbl_distribution ON tbl_seasons.season_id = tbl_distribution.season_id
        WHERE tbl_seasons.user_id = ?
        GROUP BY season_name");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'crop_name' => $row['season_name'],
                'total_quantity' => $row['total_quantity']
            );
        }
        $userDashboardData['distributionQuantityOfCropsPerSeason_barchart1'] = $data;

        //DISTRIBUTION QUANTITY OF DIFFERENT CROPS
        $stmt = $this->conn->prepare("SELECT crop_name, SUM(tbl_distribution.quantity) AS total_quantity FROM tbl_crops
        JOIN tbl_distribution ON tbl_crops.crop_id = tbl_distribution.crop_id
        WHERE tbl_crops.user_id = ?
        GROUP BY crop_name");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data1 = array();
        while ($row = $result->fetch_assoc()) {
            $data1[] = array(
                'crop_name' => $row['crop_name'],
                'total_quantity' => $row['total_quantity']
            );
        }
        $userDashboardData['distributionQuantityOfDifferentCrops_barchart2'] = $data1;


        //LINE CHART DISTRIBUTION TRENDS OVER TIME FOR SPECIFIC CROPS
        $stmt = $this->conn->prepare("SELECT s.start_date, c.crop_name, SUM(d.quantity) AS total_quantity
        FROM tbl_seasons s
        JOIN tbl_distribution d ON s.season_id = d.season_id
        JOIN tbl_crops c ON d.crop_id = c.crop_id
        WHERE c.user_id = ?
        GROUP BY s.start_date, c.crop_name
        ORDER BY s.start_date");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data4 = array();
        while ($row = $result->fetch_assoc()) {
            $data4[] = array(
                'start_date' => $row['start_date'],
                'crop_name' => $row['crop_name'],
                'total_quantity' => $row['total_quantity']
            );
        }
        $userDashboardData['distributionTrendsOverTimeForSpecificCrops_linechart1'] = $data4;

        //LINE CHART PLANTING HARVESTING TRENDS OVER TIME
        $stmt = $this->conn->prepare("SELECT crop_name, COUNT(tbl_crops.crop_id) AS crop_count FROM tbl_seasons
        LEFT JOIN tbl_crops ON tbl_seasons.season_id = tbl_crops.crop_id
        WHERE tbl_seasons.user_id = ?
        GROUP BY season_name");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data2 = array();
        while ($row = $result->fetch_assoc()) {
            $data2[] = array(
                'crop_name' => $row['crop_name'],
                'crop_count' => $row['crop_count']
            );
        }
        $userDashboardData['plantingHarvestingTrendsOverDifferentSeasons_linechart2'] = $data2;


        //PIE CHART CROP QUANTITY/SIZE PER SEASON
        $stmt = $this->conn->prepare("SELECT season_name, SUM(tbl_distribution.quantity) AS total_quantity FROM tbl_seasons
        INNER JOIN tbl_distribution ON tbl_seasons.season_id = tbl_distribution.season_id
        WHERE tbl_seasons.user_id = ?
        GROUP BY season_name");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data3 = array();
        while ($row = $result->fetch_assoc()) {
            $data3[] = array(
                'season_name' => $row['season_name'],
                'quantity' => $row['total_quantity']
            );
        }
        $userDashboardData['distributionOfCropsInDifferentSeasons_piechart'] = $data3;

        $responseData = array(
            "data" => $userDashboardData,
            "status" => true,
        );

        return $responseData;
    }
}
