<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $students = [];

    for ($i = 1; $i <= 10; $i++) {
        $name = $_POST["studentName$i"] ?? '';
        $grades = [
            $_POST["englishGrade$i"] ?? 0,
            $_POST["filipinoGrade$i"] ?? 0,
            $_POST["mathGrade$i"] ?? 0,
            $_POST["scienceGrade$i"] ?? 0,
            $_POST["PEGrade$i"] ?? 0
        ];

        $average = array_sum($grades) / count($grades);
        $mark = getMark($average);
        $remarks = getRemarks($mark);

        $students[$i] = [
            'name' => htmlspecialchars($name),
            'grades' => array_map('htmlspecialchars', $grades),
            'average' => $average,
            'mark' => $mark,
            'remarks' => $remarks
        ];
    }

    usort($students, function ($a, $b) {
        return $b['average'] <=> $a['average'];
    });

    $rank = 1;
    $prevAverage = null;
    $counter = 1; // This keeps track of the actual student position
    
    foreach ($students as &$student) {
        if ($prevAverage !== null && $student['average'] < $prevAverage) {
            $rank = $counter;
        }
        $student['rank'] = $rank;
        $prevAverage = $student['average'];
        $counter++;
    }
    unset($student); // Prevents unintended modifications due to reference
}

function getMark($average) {
    if ($average >= 90) return "A+";
    if ($average >= 85) return "A";
    if ($average >= 80) return "A-";
    if ($average >= 75) return "B+";
    if ($average >= 70) return "B";
    if ($average >= 65) return "B-";
    if ($average >= 60) return "C+";
    if ($average >= 55) return "C";
    if ($average >= 50) return "C-";
    return "F";
}

function getRemarks($mark) {
    $remarks = [
        "A+" => "Excellent",
        "A" => "Very Good",
        "A-" => "Good",
        "B+" => "Above Average",
        "B" => "Average",
        "B-" => "Below Average",
        "C+" => "Satisfactory",
        "C" => "Needs Improvement",
        "C-" => "Poor",
        "F" => "Fail"
    ];
    return $remarks[$mark] ?? "Unknown";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grading System</title>
    <link rel="stylesheet" href="styles/general.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <nav id="head-nav">
            <img id="logo" src="images/grades-svgrepo-com.svg" alt="grade-logo">
            <h2 id="head-title">Student Grading System</h2>
        </nav>
    </header>

    <main>
        <section id="input-grades">
            <form method="POST">
                <table class="grade-input">
                    <tr>
                        <th>Student Name</th>
                        <th>English</th>
                        <th>Filipino</th>
                        <th>Math</th>
                        <th>Science</th>
                        <th>PE</th>
                    </tr>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                    <tr>
                        <td><input type="text" name="studentName<?= $i ?>" placeholder="Ex. Juan Dela Cruz" required></td>
                        <td><input type="number" name="englishGrade<?= $i ?>" placeholder="Ex. 80" min="50" max="100" required></td>
                        <td><input type="number" name="filipinoGrade<?= $i ?>" placeholder="Ex. 80" min="50" max="100" required></td>
                        <td><input type="number" name="mathGrade<?= $i ?>" placeholder="Ex. 80" min="50" max="100" required></td>
                        <td><input type="number" name="scienceGrade<?= $i ?>" placeholder="Ex. 80" min="50" max="100" required></td>
                        <td><input type="number" name="PEGrade<?= $i ?>" placeholder="Ex. 80" min="50" max="100" required></td>
                    </tr>
                    <?php endfor; ?>
                </table>
                <button type="submit">Calculate Grades</button>
            </form>
        </section>

        <?php if (!empty($students)): ?>
        <section id="table-grades">
            <table>
                <tr>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>English</th>
                    <th>Filipino</th>
                    <th>Math</th>
                    <th>Science</th>
                    <th>PE</th>
                    <th>Average</th>
                    <th>Mark</th>
                    <th>Remarks</th>
                </tr>
                <?php foreach (array_slice($students, 0, 10) as $student): ?>
                <tr>
                    <td><?= $student['rank'] ?></td>
                    <td><?= htmlspecialchars($student['name']) ?></td>
                    <?php foreach ($student['grades'] as $grade): ?>
                        <td><?= htmlspecialchars($grade) ?></td>
                    <?php endforeach; ?>
                    <td><?= number_format($student['average'], 2) ?></td>
                    <td><?= $student['mark'] ?></td>
                    <td><?= $student['remarks'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>
        <?php endif; ?>
    </main>
</body>
</html>
