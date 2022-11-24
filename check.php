<?
// Скрипт проверки
    $mybd = new mysqli("127.0.0.1", "root", "root", "php_lab4",3306);
    if(!$mybd)
        die(json_encode(["ERROR"=>"BAD_SQL_CONN"]));
    if (isset($_COOKIE['id']) && isset($_COOKIE['hash_password']))                                              //Если в куки находятся данные
    {                                                                                                           //Проверяем их с соответствующими данными из БД
        $query = $mybd->query("SELECT * FROM users WHERE id = {$_COOKIE['id']} LIMIT 1");                       
        $userdata = $query->fetch_assoc();
        if(($userdata['hash_password'] != $_COOKIE['hash_password']) || ($userdata['id'] != $_COOKIE['id']) )   //Если куки не совпадают, пишем об этом
        {                                                                                                       //и удаляем куки.
            setcookie("id", "", time() - 3600*24*30*12, "/");
            setcookie("hash_password", "", time() - 3600*24*30*12, "/");
            print 'Обновите страницу для продолжения работы <br><a href="login.php">Войти</a>';
        }
        else                                                                                                    //Если все успешно, выводим приветствие
        {
            print "Доброго времени суток, ".$userdata['login'].". Всё в порядке! <br>";
            print '<form method="POST"><input name="submit" type="submit" value="Выйти"></form>';
        }
    }
    else
        print 'Войдите <br><a href="login.php">Войти</a>';
    if(isset($_POST['submit']))
    {
        $mybd->query("UPDATE users SET hash_password='' WHERE id='{$_COOKIE['id']}'");
        setcookie("id", null, -1, "/");
        unset($_COOKIE['id']);
        unset($_COOKIE['hash_password']);
        setcookie("hash_password", null, -1, "/");
        header("Location: registration.php"); exit();
    }
?>
