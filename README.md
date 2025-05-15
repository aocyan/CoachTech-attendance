<h1>CoachTech-attendance(コーチテック勤務管理)</h1>
<h2>〇　環境構築手順</h2>
<p>※OSはWindows11を使用しております。OSがMacを使用の際は適宜環境構築お願いいたします。</p>
<h3>1.クローンについて</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu内で　git@github.com:aocyan/CoachTech-attendance.git　を実行しクローンする。</p>
<h3>2.DockerDesktopの立ち上げ</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;DockerDesktopアプリを立ち上げる。</p>
<h3>3.docker-compose up -d --build　の実行</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu内で　docker-compose up -d --build　を実行する。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;(CoachTech-attendanceディレクトリ内で実行する。)</p>
<h3>4.VSCodeを起動とymlファイルの確認</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu上で　code.　を実行(CoachTech-attendanceディレクトリ内で実行する)し、"docker-compose.yml"ファイル内の<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;mysql:<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;image: mysql:8.0.26<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;environment:<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MYSQL_ROOT_PASSWORD: root<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MYSQL_DATABASE: laravel_db<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MYSQL_USER: laravel_user<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;MYSQL_PASSWORD: laravel_pass<br>
   であることを確認してください。</p>
<h3>5.composerをインストール</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu上で　docker-compose exec php bash　を実行し、PHPコンテナ上で<br>
   composer install　を実行する。</p>
<h3>6.envファイルをコピー</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;"5"に続いてPHPコンテナ上で<br>
   cp .env.example .env　を実行し、.envファイルをコピーする。</p>
<h3>7.envファイルをymlファイルに同期させる</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;"6"でコピーした"envファイル"と"ymlファイル"を同期する。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".envファイル"を<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DB_HOST=mysql<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DB_DATABASE=laravel_db<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DB_USERNAME=laravel_user<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;DB_PASSWORD=laravel_pass<br>
  に設定を変更する。<br>
  ※「'.env'を保存できませんでした。」とエラーが出た際は、ubuntu内CoachTech-attendanceディレクトリ内で<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sudo chown ユーザ名:ユーザ名 ファイル名<br>
  でファイルを書き換える権限を付与させてください。<br>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;例：sudo chown aocyan:aocyan /home/aocyan/coachtech/laravel/CoachTech-attendance/src/.env</p>
<h3>8.mysqlのデータベース確認</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;http://localhost:8080 にデータベースが存在しているか確認する（laravel_dbがあるか確認してください）</p>
<h3>9.アプリケーションキーの生成</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu内PHPコンテナ上で<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;php artisan key:generate　を実行し、アプリケーションキーを生成する。
<h3>10.シンボリックリンクを作成（ヘッダー及び一部画面でstorage使用)</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu内PHPコンテナ上で<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;php artisan storage:link　を実行し、シンボリックリンクを作成する。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;（作成済みであるというメッセージが出るかもしれませんが、一応実行してください）</p>
<h3>11.マイグレーション</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu内PHPコンテナ上で<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;php artisan migrate　を実行し、マイグレーションする。</p>
<h3>12.ダミーデータを挿入</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;ubuntu内PHPコンテナ上で<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;php artisan db:seed　を実行し、ダミーデータを挿入する。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;※1.管理者は<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;メールアドレス:admin@example.com<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;パスワード:1234abcd<br>
   &nbsp;&nbsp;&nbsp;&nbsp;に設定してあります。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;※2.全ユーザのパスワードを1234abcdに設定してありますが、メールアドレスはランダムにしてありますので、<br>
   &nbsp;&nbsp;&nbsp;&nbsp;お手数ですが、ユーザをダミーデータでテストする際は、メールアドレスはhttp://localhost:8080にアクセスして、<br>
   &nbsp;&nbsp;&nbsp;&nbsp;userテーブルに記載されているメールアドレスを使用してください。</p>
<h3>13.localhostにアクセス(エラー対策)</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;http://localhost/ にアクセスする<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;※1.permissionエラーが出た際には、ubuntu内CoachTech-fleaディレクトリで、<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sudo chmod -R 777 src/*　を実行してください。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;※2.chmod(): Operation not permittedエラーが出た際には、ubuntu内CoachTech-attendanceディレクトリで<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sudo chown -R www-data:www-data src/storage　を実行してください。<br>
   （上記2つのエラーについてはテスト時必ず出ていたため、あらかじめ2つのコマンドを実行しておいた方がよいと思われます。）</p>
<h3>14.テストケースの実行</h3>
<p>&nbsp;&nbsp;&nbsp;&nbsp;PHPコンテナ上で　php artisan test　を実行すると、すべてのテストケースを実行することができます。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;もし、個別にテストケースを実行するときは<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;php artisan test tests/Feature/テストファイル名<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;例：php artisan test tests/Feature/UserLoginTest.php<br>
   で実行してください。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;テストケース実行後にダミーデータを使用するときはPHPコンテナ上で<br>
   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;php artisan db:seed　をし直してください。<br>
   &nbsp;&nbsp;&nbsp;&nbsp;（各テストケース実行時に use RefreshDatabase; でマイグレーションし直す仕様になっています)</p>
<h2>〇　アプリの仕様について（UI素材との違い）</h2>
<h3>☆スタッフ画面について</h3>
<h4>・ヘッダーについて</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;勤怠　→  出勤状況  にリンク名を変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;勤怠一覧  →  勤務一覧  にリンク名を変更</p>
<p>ⅲ）&nbsp;&nbsp;&nbsp;&nbsp;申請　→　申請一覧　にリンク名を変更</p>
<p>  ※いずれもリンク名のみを変更し、機能については仕様書通り</p>
<h4>・勤怠画面について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;画面上部に「出勤状況」という文言を追加</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;「出勤」ボタンを「出勤する」ボタンにボタン名を変更</p>
<p>ⅲ）&nbsp;&nbsp;&nbsp;&nbsp;「退勤」ボタンを「退勤する」ボタンにボタン名を変更</p>
<p>ⅳ）&nbsp;&nbsp;&nbsp;&nbsp;「休憩入」ボタンを「休憩する」ボタンにボタン名を変更</p>
<p>ⅴ）&nbsp;&nbsp;&nbsp;&nbsp;「休憩戻」ボタンを「休憩終わり」ボタンにボタン名を変更</p>
<p>ⅵ）&nbsp;&nbsp;&nbsp;&nbsp;お疲れ様でした。　→　一日お疲れさまでした　に文言変更</p>
<p>ⅶ）&nbsp;&nbsp;&nbsp;&nbsp;画面下部に「ログアウトする」ボタンを追加（ユーザはログアウトすることができる）</p>
<h4>・勤怠一覧画面について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;┃ 勤怠一覧　→　┃ 勤務一覧　に文言変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;出勤　→　出勤時間　に文言変更</p>
<p>ⅲ）&nbsp;&nbsp;&nbsp;&nbsp;退勤　→  退勤時間　に文言変更</p>
<p>ⅳ）&nbsp;&nbsp;&nbsp;&nbsp;休憩　→  休憩時間　に文言変更</p>
<p>ⅴ）&nbsp;&nbsp;&nbsp;&nbsp;合計　→　実働時間　に文言変更</p>
<p>ⅵ）&nbsp;&nbsp;&nbsp;&nbsp;詳細　→　詳細ページへ　にリンク名変更</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;※いずれも文言及びリンク名のみを変更し、機能については仕様書通り</p>
<h4>・勤怠詳細画面について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;┃ 勤怠詳細　→　┃ 勤務詳細　に文言変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;休憩記入欄については、休憩時間があった日は休憩時間をさらに１つ加えて修正申請できるようにするとともに<br>
       &nbsp;&nbsp;&nbsp;&nbsp;休憩時間がない日については、新たに３つ修正申請できるようにしている。</p>
<p>ⅲ）&nbsp;&nbsp;&nbsp;&nbsp;承認機能について、ユーザが修正申請をして、管理者がこれを承認した場合、再度ユーザがその日の詳細画面を<br>
       &nbsp;&nbsp;&nbsp;&nbsp;開いたとき、「※　管理者が承認済みです」といった文言に加えて、さらに再度修正申請できるようにしている。</p>
<h4>・申請一覧画面について</h4>
<p>&nbsp;&nbsp;&nbsp;&nbsp;詳細　→　詳細ページへ　にリンク名変更</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;※リンク名のみを変更し、機能については仕様書通り</p>
<h3>☆管理者画面について</h3>
<h4>・ヘッダーについて</h4>
<p>&nbsp;&nbsp;&nbsp;&nbsp;勤怠一覧　→  勤務一覧　にリンク名変更</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;※リンク名のみを変更し、機能については仕様書通り</p>
<h4>・勤怠一覧画面について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;┃ 〇年〇月〇日の勤怠　→　┃ 〇年〇月〇日の勤務一覧　に文言変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;出勤　→　出勤時間　に文言変更</p>
<p>ⅲ）&nbsp;&nbsp;&nbsp;&nbsp;退勤　→  退勤時間　に文言変更</p>
<p>ⅳ）&nbsp;&nbsp;&nbsp;&nbsp;休憩　→  休憩時間　に文言変更</p>
<p>ⅴ）&nbsp;&nbsp;&nbsp;&nbsp;合計　→　実働時間　に文言変更</p>
<p>ⅵ）&nbsp;&nbsp;&nbsp;&nbsp;詳細　→　詳細ページへ　にリンク名変更</p>
<p>ⅶ）&nbsp;&nbsp;&nbsp;&nbsp;カレンダーアイコンをクリックして、勤務一覧の日付を検索できるようにする。</p>
<h4>・勤怠詳細画面(管理者権限による修正）について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;┃ 勤怠詳細　→　┃ 勤務詳細　に文言変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;休憩記入欄については、休憩時間があった日は休憩時間をさらに１つ加えて修正できるようにするとともに<br>
      &nbsp;&nbsp;&nbsp;&nbsp;休憩時間がない日については、新たに３つ修正できるようにしている。</p>
<p>ⅲ)&nbsp;&nbsp;&nbsp;&nbsp;「修正」ボタン名を「修正する」にボタン名変更
<p>ⅳ)&nbsp;&nbsp;&nbsp;&nbsp;「勤務一覧」ボタンを追加して、勤務一覧に容易に戻れるようにする。</p>
<h4>・スタッフ一覧画面について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;月次勤怠　→　月次勤務　に文言変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;「詳細」リンク名を「月次勤務ページへ」にリンク名変更</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;※いずれも文言及びリンク名のみを変更し、機能については仕様書通り</p>
<h4>・スタッフ別勤怠一覧画面について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;┃ 〇〇さんの勤怠　→　┃ 〇〇さんの月次勤務　に文言変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;出勤　→　出勤時間　に文言変更</p>
<p>ⅲ）&nbsp;&nbsp;&nbsp;&nbsp;退勤　→  退勤時間　に文言変更</p>
<p>ⅳ）&nbsp;&nbsp;&nbsp;&nbsp;休憩　→  休憩時間　に文言変更</p>
<p>ⅴ）&nbsp;&nbsp;&nbsp;&nbsp;合計　→　実働時間　に文言変更</p>
<p>ⅵ）&nbsp;&nbsp;&nbsp;&nbsp;詳細　→　詳細ページへ　にリンク名変更</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;※いずれも文言及びリンク名のみを変更し、機能については仕様書通り</p>
<h4>・申請一覧画面について</h4>
<p>&nbsp;&nbsp;&nbsp;&nbsp;詳細　→　詳細ページへ　にリンク名変更</p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;※リンク名のみを変更し、機能については仕様書通り</p>
<h4>・勤怠詳細画面(修正申請の承認）について</h4>
<p>ⅰ）&nbsp;&nbsp;&nbsp;&nbsp;┃ 勤怠詳細　→　┃ 勤務詳細　に文言変更</p>
<p>ⅱ）&nbsp;&nbsp;&nbsp;&nbsp;「修正」ボタン名を「修正する」にボタン名変更</p>
<p>ⅲ）&nbsp;&nbsp;&nbsp;&nbsp;「承認」ボタンを「承認する」ボタンにボタン名を変更</p>
<p>ⅳ）&nbsp;&nbsp;&nbsp;&nbsp;「勤務一覧」ボタンを追加して、勤務一覧に容易に戻れるようにする。</p>
<h2>〇　その他</h2>
<p>&nbsp;&nbsp;&nbsp;&nbsp;自動メール認証は実装しておりません。</p>
<h2>〇  使用技術</h2>
<p>・PHP:ver.7.4.9</p>
<p>・Laravel:ver.8.83.29</p>
<p>・MySQL:ver.15.1</p>
<p>・Composer:ver.2.8.4</p>
<p>・ubuntu:ver.24.04.1 LTS</p>
<h2>〇　ER図</h2>
![coachtechAttendance](https://github.com/user-attachments/assets/e5f84b20-5bd5-4563-b09e-5229109e3db2)

![coachtechAttendance](https://github.com/user-attachments/assets/9c98fd14-5d70-4a2d-b93c-17c5aa1c8047)
<h2>〇　URL</h2>
<p>・開発環境: http://localhost/</p>
<p>・phpMyAdmin： http://localhost:8080/</p>
