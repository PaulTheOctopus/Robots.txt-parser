<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title>RobotsTxtChecker</title>
</head>
<body>
<script>
$(document).ready(function(){
    $('.button').click(function(){
        var clickBtnValue = $(this).val();
        var ajaxurl = 'get.php',
        data =  {'action': clickBtnValue};
        $.post(ajaxurl, data, function (response) {
            // Response div goes here.
            alert("action performed successfully");
        });
    });

});
</script>

<!--_________________________________________-->
<div class="container">
    <div class="row">
        <div class="col-md-auto mx-auto">
            <h1>Type in URL</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-auto mx-auto mt-10">
            <form action="get.php" method="POST">
                <input name="URL" type="text" placeholder="URL">
                <input type='submit' value='Отправить'>
                <input class="button" type='submit' name="download" value='скачать'>
            </form>
        </div>
    </div>
</div>


<?php

global $response, $hostNumb, $isSitemap, $resultfile;

if(!empty($_POST['URL'])) {
$getfile = $_POST['URL'] . '/robots.txt'; // добавляем имя файла
$file_headers = @get_headers($getfile); // подготавливаем headers страницы
 
if ($file_headers[0] != 'HTTP/1.1 200 OK') {
 
    $response = $file_headers[0];
 
} else if ($file_headers[0] == 'HTTP/1.1 200 OK') {
    $response = $file_headers[0];
    // открываем файл для записи
    $file = fopen('robots.txt', 'w');
    // инициализация cURL
    $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $getfile);
          curl_setopt($ch, CURLOPT_FILE, $file);
          curl_exec($ch);
          fclose($file);
          curl_close($ch);
 
       $resultfile = 'robots.txt';
 
if (!file_exists($resultfile)) {
    // Если файл отсутвует, сообщаем ошибку
    //echo "Ошибка обработки файла: $resultfile";
    $resultfile = "Ошибка"; 
 
} else {
    // Начинаем обрабатывать файл, если все прошло успешно
    // $file_arr = file("robots.txt");
    $textget = file_get_contents($resultfile);
               htmlspecialchars($textget); // при желании, можно вывести на экран через echo
 
    if (preg_match("/Host/", $textget)) {
        $count = substr_count($textget,'Host');
        $hostNumb = "Количество директив Host: ". $count . " ";
    } else {
        $hostNumb = "Дерективы Host нет. ";
    }

    if (preg_match("/Sitemap/", $textget)) {
        $isSitemap = "Директива Sitemap указана";
    } else {
        $isSitemap = "В файле robots.txt не указана директива Sitemap";
    }
 
    //echo 'Размер файла ' . $resultfile . ': ' . filesize($resultfile) . ' байт';
 
}
}
} else {
  echo 'Вы ничего не ввели :(';
}

//первая строка
echo "

<table class=\"table table-bordered\">
  <thead>
    <tr>
      <th scope=\"col\">Название проверки</th>
      <th scope=\"col\">Статус</th>
      <th scope=\"col\"></th>
      <th scope=\"col\">Текущее состояние</th>
    </tr>
  </thead>
  <tbody>
    <tr>";

    $verifiTitle = array(
    "Проверка наличия файла robots.txt",
    "Проверка указания директивы Host", 
    "Проверка количества директив Host, прописанных в файле", 
    "Проверка размера файла robots.txt",
    "Проверка указания директивы Sitemap",
    "Проверка кода ответа сервера для файла robots.txt"
    );

    $okState = array(
        "Файл robots.txt присутствует",
        "Директива Host указана", 
        "В файле прописана 1 директива Host", 
        "Размер файла robots.txt составляет $, что находится в пределах допустимой нормы",
        "Директива Sitemap указана",
        "Файл robots.txt отдаёт код ответа сервера 200"
        );
    
    $errState = array(
        "Файл robots.txt отсутствует",
        "В файле robots.txt не указана директива Host", 
        "В файле прописано несколько директив Host", 
        "Размера файла robots.txt составляет $, что превышает допустимую норму",
        "В файле robots.txt не указана директива Sitemap",
        "При обращении к файлу robots.txt сервер возвращает код ответа $"
        );
    
    $errRecommend = array(
        "Программист: Создать файл robots.txt и разместить его на сайте.", 
        "Программист: Для того, чтобы поисковые системы знали, какая версия сайта 
        является основных зеркалом, необходимо прописать адрес основного зеркала в 
        директиве Host. В данный момент это не прописано. Необходимо добавить в файл 
        robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.",
        "Программист: Директива Host должна быть указана в файле толоко 1 раз. Необходимо 
        удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую 
        основному зеркалу сайта", 
        "Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. Необходимо 
        отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб",
        "Программист: Добавить в файл robots.txt директиву Sitemap",
        "Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. 
        Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200"
        );
    
    function ok($i, $okState, $resultfile)
    {
        echo "OK";
        echo "
        </td>
            <td>Состояние</td>";
            
            if ($i != 3) {
               echo "<td>".$okState[$i]."</td>";
            }else {
                echo "<td>".str_replace("$", filesize($resultfile), $okState[$i])."</td>";
            }
        echo "
        </tr>
        <tr>
        <td>Рекомендации</td>
        <td>Доработки не требуются</td>
        </tr>
        ";
    }

    function neOk($i, $errState, $errRecommend, $response, $resultfile)
    {
        echo "Ошибка";
        echo "
        </td>
            <td>Состояние</td>
            
            <td>";
            if($i != 3 || $i != 5){
                echo $errState[$i];
            }elseif($i == 3){
               echo str_replace("$", filesize($resultfile), $errState[$i]);
            }elseif($i == 5){
                echo str_replace("$", $response, $errState[$i]); 
             }
            echo "</td>
        </tr>
        <tr>
        <td>Рекомендации</td>";
        echo "<td>".$errRecommend[$i]."</td>
        </tr>
        ";
    }



    function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
        $f = fopen('php://memory', 'w');
        foreach ($array as $line) {
            fputcsv($f, $line, $delimiter); 
        }
        fseek($f, 0);
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'";');
        fpassthru($f);
    }



    for ($i = 0; $i < 6; $i++) {
    echo "<td rowspan=2>";
    echo $verifiTitle[$i];
    echo "</td>
        <td rowspan=2>";
        switch ($i) {
            case 0:
                if (file_exists($resultfile)) {
                    ok($i, $okState, $resultfile);
                }else {
                    neOk($i, $errState, $errRecommend, $response, $resultfile);
                }
                break;
            case 1:
                if ($hostNumb != 0) {
                    ok($i, $okState, $resultfile);
                }else {
                    neOk($i, $errState, $errRecommend, $response, $resultfile);
                }
                break;
            case 2:
                if ($hostNumb != 1 && $hostNumb > 0) {
                    ok($i, $okState, $resultfile);
                }elseif($hostNumb == 0) {
                    neOk($i,0,0,0,0);
                }else {
                    neOk($i, $errState, $errRecommend, $response, $resultfile);
                }
                break;
            case 3:
                if (filesize($resultfile) <= 32000) {
                    ok($i, $okState, $resultfile);
                }elseif (filesize($resultfile) == 0) {
                    neOk($i,0,0,0,0);
                }else {
                    neOk($i, $errState, $errRecommend, $response, $resultfile);
                }
                break;
            case 4:
                if ($isSitemap == "Директива Sitemap указана") {
                    ok($i, $okState, $resultfile);
                }else {
                    neOk($i, $errState, $errRecommend, $response, $resultfile);
                }
                break;
            case 5: 
                if ($response == "HTTP/1.1 200 OK") {
                    ok($i, $okState, $resultfile);
                }else {
                    neOk($i, $errState, $errRecommend, $response, $resultfile);
                }
                break;
        }
    }    
  echo "</tbody>
</table>";

 //--------------------------------------------------   

?>

</body>
</html>

