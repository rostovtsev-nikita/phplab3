<?
// Страница авторизации
    function generateHash($length=6)                                                                           //Функция создания случайной хешированной строки
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        return hash_password(substr(str_shuffle($chars), 0, $length),PASSWORD_BCRYPT);
    }
    $mybd = new mysqli("127.0.0.1", "root", "root", "php_lab4",3306);
    if(!$mybd)
        die(json_encode(["ERROR"=>"BAD_SQL_CONN"]));
    if(isset($_POST['submit']))
    {
        if( $_POST['password'] && $_POST['login']){                                                            
            $query = $mybd->query("SELECT id, password FROM users WHERE login='{$_POST['login']}' LIMIT 1");
            $data = $query->fetch_assoc();
            if(password_verify($_POST['password'],$data['password']))                                           //Проверка пароля с паролем из базы данных
            {                                                                                                   //
                $hash = generateHash(10);
                $mybd->query("UPDATE users SET hash_password='{$hash}' WHERE id='{$data['id']}'");
                setcookie("id", $data['id'], time()+60*60*24*30);
                setcookie("hash_password", $hash, time()+60*60*24*30);
                header("Location: check.php"); exit();
            }
            else
                print "Вы ввели неправильный логин/пароль";
        }
        else
            print "Вы ввели неправильный логин/пароль";
    }

    if (isset($_COOKIE['id']) && isset($_COOKIE['hash_password']))                                              //Если вход выполнен, переадресовываем на check.php
    {                                                                                                           
        $query = $mybd->query("SELECT * FROM users WHERE id = {$_COOKIE['id']} LIMIT 1");
        $userdata = $query->fetch_assoc();
        if(($userdata['hash_password'] == $_COOKIE['hash_password']) && ($userdata['id'] == $_COOKIE['id']))
        { 
            header("Location: check.php"); 
            exit();
        }
    }
?>
<form method="POST">
    Логин <input name="login" type="text"><br>
    Пароль <input name="password" type="password"><br>
    <button name="submit" type="submit">Войти</button>
</form>
<a href="registration.php">Зарегистрироваться</a>
