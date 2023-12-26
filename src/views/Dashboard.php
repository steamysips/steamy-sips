<main class="container">
    <h1>Dashboard</h1>

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
    <a href="<?= ROOT ?>/register"><Button>Create new user</Button></a>
</main>