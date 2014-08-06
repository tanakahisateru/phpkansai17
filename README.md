# TODO管理アプリケーションチュートリアル

これはTODO管理アプリケーションを作りながら PhpStorm を学ぶための素材です。

書かれているコードは記述が面倒な一部のものしかありません。実際にアプリケーションを開発するには、
このドキュメントだけでは知識が不足しています。あくまで、私的なトレーニングのためのものですのであしからず。


## 最初に

### 必要なもの

- PHP >= 5.4
- NodeJS
- LESS
- CoffeeScript
- できれば Mac

### 推奨プラグイン

- .gitignore support : .gitignoreファイルでパスを補完できます
- Markdown : このドキュメントをIDE内で参照するとき少し読みやすくなります
- PHPUnit code coverage : テストのカバレッジが確認できます (後でつかいます)
- PHP Advanced AutoComplete : 補完の幅が広がります

### 先にやっておく手順

PhpStorm の `Option + F12` でターミナルを開きます。

[Composer](https://getcomposer.org/) をダウンロードします。

```
curl -sS https://getcomposer.org/installer | php
```

Composer を使って依存ライブラリをインストールします。

```
php composer.phar install
```

先の説明を読む前に、ここまでをそのまま実行しておきましょう。ダウンロードには時間がかかるので。

最初にインストールされるもの:

- [Silex](http://silex.sensiolabs.org/)
- [Twig](http://twig.sensiolabs.org/)
- [Doctrine/DBAL](http://www.doctrine-project.org/projects/dbal.html)


## 実装する機能の仕様

TODOリストを管理するアプリケーションを作成します。

TODOリストとは `content` (内容) と `checked` (チェック済み) のプロパティを持つデータのリストです。

HTTPのAPI仕様は次のようにします。

- `/` を GET するとTODOリストを確認できます。
- `/todo` に POST するとTODOリストに追加できます。
- `/todo/{id}/check` に PATCH すると、id で指し示す項目の `checked` 状態を変更できます
- `/todo/{id}` を DELETE することで、id で指し示す項目をひとつ削除できます

※ Webのフォームでは PUT/DELETE することができないので、Symfony コンポーネントの `HttpFoundation\Request` で
[enableHttpMethodParameterOverride](http://api.symfony.com/2.5/Symfony/Component/HttpFoundation/Request.html#method_enableHttpMethodParameterOverride)
を利用して、POST を擬似的に別のHTTPメソッドに見立てることにしています。

これらのエントリポイントは `/src/Controller/TodoController.php` にあります。
実行できるようになったら、サンプルアプリケーション内の `indexAction` の反応を確かめましょう。


## サンプルアプリケーションの構造

### Web公開フォルダ

- /public

ここはWebフォルダのルートです。PhpStorm で "Mark Directory As" &gt; "Resource Root" としておきましょう。

### アプリケーションの基礎

- /public/index.php
- /src/config.php
- /src/Controller/TodoController.php
- /src/Model/Todorepository.php
- /views/*

`index.php` は Silex の基本設定を済ませたエントリポイントです。
SQLite を使って `/var/data.sqlite` にアクセスし、データを保存するようにしています。
また、Twigとオブジェクトへのルーティング、デバッグ、HTMLフォームのHTTPメソッド対応も行っています。

`/src` はアプリケーション設計に固有の設定を含みます。
`composer.json` には `/src` を名前空間の `PhpKansai\TodoManager\` に対応させる設定があり、この中のクラスは PSR-4 にしたがってロードされます。

`/views` は Twig テンプレートファイルがロードされる場所になります。

### テスト

- /tests/*

ここはWebフォルダのルートです。PhpStorm で "Mark Directory As" &gt; "Test Source Root" としておきましょう。


## 実行

### サーバー

PhpStorm のターミナルを使い、PHPのビルトインサーバーでWebアプリケーションをテスト実行します。

```
php -S 0.0.0.0:8080 -t public
```

これだけで、[localhost:8080](http://localhost:8080/) をブラウザで閲覧できます。
終了するのを忘れていても、PhpStorm を終了させればサーバプロセスは停止します。


### テスト

プロジェクトの PHP の設定で Include path に `vendor/phpunit/phpunit` が含まれているかを確認します。
どのPHPインタプリタを使うかも、正しく指定しましょう。

確認できたら `phpunit.xml` を右クリックして `Run phpunit.xml` 実行。
以降は同じ設定で `^ + R` (直前のプログラム実行を繰り返す) で繰り返し実行できます。


## フロントエンド側の準備

- [jQuery](http://jquery.com/)
- [Bootstrap 3](http://getbootstrap.com/)
- [Bootbox](http://bootboxjs.com/) (綺麗なダイアログボックスのために)

[Component Installer](http://robloach.github.io/component-installer/) を利用して、`/public/assets` 以下にインストールするのが簡単です。

`composer.json` に以下を追加して、依存を更新ます。

```json
{
    "require": {
        "components/bootstrap": "~3.2",
        "components/jquery": "~2.1",
        "components/bootbox": "dev-master"
    },
    "config": {
        "component-dir": "public/assets",
        "component-baseurl": "/assets"
    }
}
```

`public/mock.html` など任意のファイルを作成して、まずは、PHPの挙動と関係なくHTMLコーディングを試しましょう。
Bootstrap のナビゲーションバーが表示されるか試してみます。

```html
<div class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">TODO管理</a>
        </div>
    </div>
</div>
```

jQuery などのスクリプトもすべて `</body>` 直前でリンクし、ブラウザのデバッガから動作を確認しておきます。


## データベース

PhpStorm のデータベースツールを使います。
データソースの追加で、SQLite のドライバに Xerial を選択します。
パスが `/var/data.sqlite` となるように設定します。

プロジェクトのSQL方言の設定を SQLite のものに変更します。

その状態で、`schema.sql` を右クリックして実行しましょう。
作成できるテーブル対して、テーブルエディタ、SQLコンソールでいろいろ操作を確認してみます。


## Git

自分のブランチを作り、作業の区切りごとにコミットを積むようにします。
`Command + 9` で、つねに変更しているファイルが見えるようにしながら作業しましょう。
コミットする前に何を変更したかを差分表示で確認し、無駄な変更(離れたところに改行を追加など)は綺麗にします。

コミット時のコードレビュー結果は無視しないようにしましょう。
コードレビューに警告が出すぎて見なくなる前に、エディタの右の黄色いマーカーを減らすようにつとめましょう。


## TODOモデルの作成

モデルの設計方針として、エンティティとリポジトリの2クラスでの構成を考えます。
それぞれのクラスをテスト駆動開発してみましょう。

### TodoEntity
`PhpKansai\TodoManager\Model\TodoEntity` クラスを作成し、対応するテストを `/tests` に作成します。

ファクトリメソッド `create()` をテストファーストで作ります。

```php
     public function testCreate()
     {
         $todo = TodoEntity::create("test");
         $this->assertEquals("test", $todo->content);
     }
```

それから、 `check()` および `uncheck()` メソッドについてもテストファーストで。

```php
     public function testCheck()
     {
         $todo = TodoEntity::create("test");
         $todo->checked = false;
         $todo->check();
         $this->assertTrue($todo->checked);
     }

     public function testUnckeck()
     {
         $todo = TodoEntity::create("test");
         $todo->checked = true;
         $todo->uncheck();
         $this->assertFalse($todo->checked);
     }
```

自分の書いた実装コードには、 **かならず** Docコメントを書きましょう。これはコード補完のためだけでなく、型の検査のためにも重要です。

ときおり、PHPUnit code coverage プラグインでテストのカバレッジを確認します。

### TodoRepository
`PhpKansai\TodoManager\Model\TodoRepository` クラスを作成し、対応するテストを `/tests` に作成します。
`setUp` と `tearDown` では `TestDatabaseHelper` を利用しましょう。

新しく登録する `append()` メソッド、取得に `findAll()`, `findById()`、状態を同期する `syncCheckStatus()`、
そして削除の `remove()` ...といったメソッドを作ります。

```php
     public function testAppend()
     {
         $todo = TodoEntity::create("test");
         $this->repository->append($todo);

         $this->assertNotNull($todo->id);

         $num = $this->repository->getConnection()->fetchColumn(
             "SELECT COUNT(*) FROM todo;"
         );
         $this->assertEquals(1, $num);
     }

     public function testFindAll()
     {
         $this->repository->append(TodoEntity::create("test1"));
         $this->repository->append(TodoEntity::create("test2"));
         $this->repository->append(TodoEntity::create("test3"));

         $all = $this->repository->fiondAll();

         $this->assertCount(3, $all);
         $this->assertEquals("test1", $all[0]->content);
         $this->assertEquals("test2", $all[1]->content);
         $this->assertEquals("test3", $all[2]->content);
     }

     public function testFindById()
     {
         $todo = TodoEntity::create("test");
         $this->repository->append($todo);

         $notfound = $this->repository->findById(1000);
         $this->assertNull($notfound);

         $restored = $this->repository->findById($todo->id);
         $this->assertNotNull($restored);
         $this->assertEquals("test", $restored->content);
     }

     public function testSyncCheckStatus()
     {
         $todo = TodoEntity::create("test");
         $this->repository->append($todo);

         $todo->check();
         $this->repository->syncCheckStatus($todo);

         $restored = $this->repository->findById($todo->id);
         $this->assertTrue($restored->checked);

         $todo->uncheck();
         $this->repository->syncCheckStatus($todo);

         $restored = $this->repository->findById($todo->id);
         $this->assertFalse($restored->checked);
     }

     public function testRemove()
     {
         $todo = TodoEntity::create("test");
         $this->repository->append($todo);
         $id = $todo->id;

         $this->repository->remove($todo);

         $restored = $this->repository->findById($id);
         $this->assertNull($restored);
     }
```


## コントローラー

### indexAction

`TodoController` の `indexAction` で Twig を使った HTML レンダリングを行います。

`Twig_Environment` と `TodoRepository` のインスタンスがそれぞれ `twig` および `todo.repository`
として `$app` に登録されているので、これらをコントローラーのフィールドに保持します。
(フィールドには型を宣言することができるので)

リポジトリから `findAll()` した結果を `index.html.twig` に `entities` として出力しましょう。
ビューではひとまず Twig で `{{dump(entities)}}` として変数の内容を確認します。


## HTML

Bootstrap を試したHTMLファイルを参考に `layout.html.twig` を書き、`index.html.twig` でそれを継承します。

`index.html.twig` の基本的なレイアウトは以下をコピーして使います。

```html
<div class="container">
    {% if error is defined %}
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            {{ error | nl2br }}
        </div>
    {% endif %}
    <div class="row">
        <div class="col-sm-7 col-md-8">
            <div class="panel panel-default">
                <table class="table panel-body todo-list">
                    <tr>
                        <td>ここにデータを表示</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="col-sm-5 col-md-4">
            <p>ここにフォームを作る</p>
        </div>
    </div>
</div>
```

データベースツールでデータを作成し、この `<table>` にレンダリングします。


## 投稿フォームの実装

### appendAction

フォームから投稿される `/todo` への POST を実装します。

```php
   /**
    * POST:"todo"
    */
   public function appendAction(Request $request)
   {
       $content = $request->request->get('content');
       if (!is_null($content)) {
           // ここで新規TODOを保存
           return $this->app->redirect('/');
       } else {
           return ...; // エラー応答
       }
   }
```

まだUIがない段階ですが、PhpStorm の REST Client を使っていったんこのリクエストを試してみます。
レスポンスとデータが保存されていることを確認します。

フォームUIのHTMLにはこれをコピーして使います。

```html
<form action="/todo" method="post">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="form-group">
                <label class="sr-only" for="todo-content">内容</label>
                <textarea class="form-control" name="content" placeholder="新しいTODO"></textarea>
            </div>
        </div>
        <div class="panel-footer">
            <button class="btn btn-primary">追加</button>
        </div>
    </div>
</form>
```

`public/css/app.less` を作成し、テキストエリアの高さを調整します。
サジェストにしたがってPhpStorm のウォッチャーを設定しましょう。`*.less` のウォッチをしすぎないよう、ウォッチャーで使うスコープを定義して選択しないといけません。


## Ajaxによるデータ更新

### checkAjaxAction

`checkAjaxAction()` を実装します。
これは、HTTPの PATCH を `/todo/{id}/check?checked=...` の形式で受け取り、新しいエンティティをJSON形式でひとつ返します。
REST Client を使い、`checked=0` の場合と `checked=1` の場合を確かめながら作りましょう。

Ajax リクエストの呼び出し側の実装は面倒なので、jQueryプラグインを使います。

CoffeeScript で書かれた jQuery プラグイン、`single-button-action.coffee` (このプロジェクト専用) を `/public/js` に設置します。
これもまた、 PhpStorm のウォッチャーで変換するようにし、スコープを確認します。

```coffee
###
クリック1発でPOSTやPUTなどのリクエストを

Attributes:
data-url required
data-method optional
data-confirm optional

<button data-url="/path/to/action" data-method="DELETE" data-confirm="Are you sure?">Delete</button>

$buttons.singleButtonAction({
    // optional
    prepare: function() {
        return { ... extra params to send };
    }
});
###
$.fn.singleButtonAction = (config)->
    $(this).on 'click', (event)->
        event.preventDefault()

        exec = =>
            url = $(this).data 'url'
            method = $(this).data 'method' ? 'post'
            data = if config?.prepare? then config.prepare.call this else null

            form = $('<form action="' + url + '">')
            switch method.toLowerCase()
                when 'get'
                    form.attr 'method', 'get'
                when 'post'
                    form.attr 'method', 'post'
                else
                    form.attr 'method', 'post'
                    form.append(
                        $('<input type="hidden" name="_method">').val method.toUpperCase()
                    )
            if data?
                for k, v in data
                    form.append(
                        $('<input type="hidden">').attr('name', k).val v
                    )
            form.submit()

        if $(this).data 'confirm'
            bootbox.confirm ($(this).data 'confirm'), (ok)->
                if ok
                    exec()
        else
            exec()

###
クリック1発でGET以外のリクエストを

Attributes:
data-url required
data-method optional
data-confirm optional

<button data-url="/path/to/action" data-method="DELETE" data-confirm="Are you sure?">Delete</button>

$buttons.singleButtonAjaxAction({
    // optional
    prepare: function() {
        return { ... extra params to send };
    },
    // optional
    done: function(data) {
        // succeeded handler
    },
    // optional
    fail: function() {
        // failed handler
    }
});
###
$.fn.singleButtonAjaxAction = (config)->
    $(this).on 'click', (event)->
        event.preventDefault()

        exec = =>
            url = $(this).data 'url'
            method = $(this).data 'method'
            data = if config?.prepare? then config.prepare.call this else null

            context = {}
            context.type = method ? 'POST'
            if data?
                context.data = data

            $.ajax url, context
                .done (data)=>
                    if config?.done then config.done.call this, data
                .fail =>
                    if config?.fail then config.fail.call()

        if $(this).data 'confirm'
            bootbox.confirm ($(this).data 'confirm'), (ok)->
                if ok
                    exec()
        else
            exec()

```

それぞれのTODOエンティティの隣にチェックボックスをつけて、`data-url` と `data-method` を指定。

```html
<td>
    <input class="complete-todo-check" type="checkbox"
           data-url="/todo/{{ todo.id }}/check" {% if todo.checked %}checked{% endif %}
           data-method="PATCH" />
</td>
```

そのチェックボックスを jQuery プラグインで加工、送信データの集め方、成功/失敗時のリアクションを割り当てます。

```html
<script type="text/javascript" src="/js/single-button-action.js"></script>
<script type="text/javascript">
    $(function() {
        $('.complete-todo-check').singleButtonAjaxAction({
            prepare: function() {
                return {
                    checked: Number($(this).prop('checked'))
                };
            },
            done: function(data) {
                $(this).prop('checked', data.checked);
            },
            fail: function() {
                bootbox.alert("通信エラー。リロードしたほうがいいかも。");
            }
        });
    });
</script>
```

jQuery のコードを書く前に、JavaScript で使用するライブラリを追加すると便利です。jQuery のセレクタと DOM がリンクします。


## 仕上げ(削除機能)

### removeAction

ここまでの流れにならって、こんどはコントローラーの `removeAction()` で DELETE に応答できるようにしてみましょう。

おそらく、「エンティティをひとつ取得して、なければ 404 Not Found で中断する」ロジックが重複して存在することになります。
PhpStorm のリファクタリング機能でメソッドを抽出し、共有するようにしましょう。

HTMLは、`single-button-action.coffee` プラグインを使ってこのように書けます。(削除は元に戻せないので確認ダイアログ付きです)

```html
<td>
    <button class="btn btn-link remove-todo-button"
            data-url="/todo/{{ todo.id }}"
            data-method="DELETE"
            data-confirm="よろしいですか"><span class="glyphicon glyphicon-trash"></span> 削除</button>
</td>
```

```html
<script type="text/javascript">
    $(function() {
        $('.remove-todo-button').singleButtonAction();
    });
</script>
```

### 品質チェック

機能がすべて作れたら、プロジェクト全体に対して Inspect Code を実行し、品質を確認します。
無意味な警告が出ている箇所はすぐに対処しておきましょう。基本的にはインスペクタの設定を変更せず、サプレッサーコメントで対応します。
ただし Typo については積極的に辞書に学習させていきましょう。

無意味な警告がなくなることで、意味のある警告が目立つようになります。


## おつかれさまでした

PhpStorm は便利ですね。
