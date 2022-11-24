<?
// Страница региcтрации
    $mybd = new mysqli("127.0.0.1", "root", "root", "php_lab4",3306);
    if(!$mybd)
        die(json_encode(["ERROR"=>"BAD_SQL_CONN"]));
    if(isset($_POST['submit']))
    {
        $err = array();
        if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['login']))                             //Проверка на соответствие шаблону 
            $err[] = "Логин может состоять только из букв английского алфавита и цифр";
        if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 35)                 //Проверка длины логина из бд
            $err[] = "Логин должен быть не меньше 3-х символов и не больше 35";
        $query = $mybd->query("SELECT id FROM users WHERE login='{$_POST['login']}'");
        if ($query->num_rows)                                                           //Проверка на возможное существование такого логина
            $err[] = "Пользователь с таким логином уже существует в базе данных";
        if(count($err) == 0)                                                            
        {                                                                               //Создание пользователя
            $login = $_POST['login'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $query =$mybd->query("INSERT INTO users (login,password,password_hash,ip) VALUES ('{$login}','{$password}',NULL,NULL)");
            header("Location: login.php"); exit();
        } 
        else
        {
            print "<h3>Не получилось зарегистрироваться:</h3><br>";
            foreach($err as $error)
                print $error."<br>";
        }
    }
?>
<form method="POST">
    Логин <input name="login" type="text"><br>
    Пароль <input name="password" type="password"><br>
    <button name="submit" type="submit">Зарегистрироваться</button>
</form>
<a href="login.php">Войти</a>
