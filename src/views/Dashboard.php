<?php
/**
 * @var array{
 *     name:string,
 *     password:string
 * } $users Array of all users as fetched from database
 */

?>
<main class="container">
    <h1>Dashboard</h1>
    <div class="grid">
        <article>
            <h6>Total sales</h6>
            <h3>Rs 9999</h3>
        </article>
        <article>
            <h6>Total orders</h6>
            <h3>9999</h3></article>
        <article>
            <h6>Total customers</h6>
            <h3>9999</h3>
        </article>

    </div>
    <h2>Sales revenue</h2>

    <div style="width: 800px;">
        <canvas id="acquisitions"></canvas>
    </div>

    <h2>Growth</h2>
    <div style="width: 800px;">
        <canvas id="aa"></canvas>
    </div>

    <h2>Recent orders</h2>
    <table>
        <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Password</th>
        </tr>
        </thead>

        <tbody>
        <?php
        foreach ($users as $user) {
            echo <<<EOL
                    <tr>
                    <td>$user->name</td>
                    <td>$user->password</td>
                    </tr>
                    EOL;
        }
        ?>
        </tbody>
    </table>
    <a href="<?= ROOT ?>/register">
        <Button>Create new user</Button>
    </a>
</main>

<script>
  (async function() {
    const data = [
      {year: 2010, count: 10},
      {year: 2011, count: 20},
      {year: 2012, count: 15},
      {year: 2013, count: 25},
      {year: 2014, count: 22},
      {year: 2015, count: 30},
      {year: 2016, count: 28},
    ];

    new Chart(
        document.getElementById("acquisitions"),
        {
          type: "bar",
          data: {
            labels: data.map(row => row.year),
            datasets: [
              {
                label: "Acquisitions by year",
                data: data.map(row => row.count),
              },
            ],
          },
        },
    );
  })();

  (async function() {
    const labels = ["January", "February", "March", "April", "May", "June", "July"];
    const data = {
      labels: labels,
      datasets: [
        {
          label: "Sales Revenue",
          data: [65, 59, 80, 81, 56, 55, 40],
          fill: false,
          borderColor: "rgb(75, 192, 192)",
          tension: 0.1,
        }],
    };
    const config = {
      type: "line",
      data: data,
    };
    new Chart(
        document.getElementById("aa"), config,
    );
  })();
</script>