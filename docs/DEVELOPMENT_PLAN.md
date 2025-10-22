# 開発計画＆進捗トラッカー

このドキュメントは、問い合わせフォームバックエンドシステムの開発計画とタスクの進捗を管理します。

---

### フェーズ1: プロジェクト基盤構築

- [x] `docs` ディレクトリと基本ドキュメント設置
- [x] Laravel Sanctum のインストールと設定
- [x] **データベースマイグレーション作成**
    - [x] `users` テーブルへのカラム追加 (`form_creation_limit`, `registration_token_id`)
    - [x] `roles`, `permissions` テーブル作成
    - [x] `role_user`, `permission_role` 中間テーブル作成
    - [x] `registration_tokens` テーブル作成
    - [x] `forms`, `inquiries`, `email_templates`, `access_tokens`, `plans` テーブル作成
- [x] **マイグレーションファイルの編集**
    - [x] 全テーブルのカラム定義を`REQUIREMENTS.md`に合わせて修正
- [x] **モデルの作成とリレーション定義**
    - [x] 全モデルの `fillable` プロパティ設定
    - [x] モデル間のリレーション (`hasMany`, `belongsToMany` 等) を定義
- [x] **シーダーの作成**
    - [x] `RolesAndPermissionsSeeder` (デフォルトの役割と権限を作成)
    - [x] `EmailTemplateSeeder` (デフォルトのメールテンプレートを作成)
    - [x] `PlansSeeder` (デフォルトのプランを作成)
    - [x] `DatabaseSeeder` で上記シーダーを呼び出し
- [x] `php artisan migrate --seed` を実行してDBを構築
---

### フェーズ2: 認証・登録機能の実装

- [x] **APIエンドポイント実装**
    - [x] `POST /register` (登録トークン検証ロジック込み)
    - [x] `POST /login` (Sanctumのトークン発行)
    - [x] `POST /logout`
- [x] **登録トークン管理 (CRUD)**
    - [x] `GET, POST /api/registration-tokens`
- [x] **テスト**
    - [x] ユーザー登録、ログイン、ログアウトのFeatureテスト
---

### フェーズ3: 権限管理機能の実装 (RBAC)

- [ ] **APIエンドポイント実装**
    - [ ] `GET, POST /api/roles`
    - [ ] `GET, PUT, DELETE /api/roles/{id}`
    - [ ] `GET /api/permissions`
    - [ ] `GET, PUT /api/users/{id}/roles`
- [ ] **認可ロジックの実装**
    - [ ] APIミドルウェアで、各エンドポイントを適切な権限で保護
- [ ] **テスト**
    - [ ] ロール・権限管理APIのFeatureテスト
    - [ ] 認可ミドルウェアのテスト
---

### フェーズ4: コア機能の実装

- [ ] **メールテンプレート管理 (CRUD)**
- [ ] **フォーム管理 (CRUD)** (デフォルトテンプレート設定ロジック込み)
- [ ] **問い合わせ受付フロー** (`/submit/{token}`)
- [ ] **問い合わせ履歴閲覧**
- [ ] **非同期メール送信ジョブ** の実装
- [ ] **テスト**
    - [ ] 各コア機能APIのFeatureテスト
---

### フェーズ5: プラン管理機能の実装

- [ ] **プラン管理 (CRUD)**
    - [ ] `GET, POST /api/plans`
    - [ ] `GET, PUT, DELETE /api/plans/{id}`
- [ ] **課金処理の実装**
    - [ ] StripeやPayPalを利用したサブスクリプション管理
    - [ ] サブスクリプション状態の定期チェックとプランの自動ダウングレード
- [ ] **テスト**
    - [ ] プラン管理と課金処理のFeatureテスト
---

### フェーズ6: 仕上げ

- [ ] APIレスポンス形式の統一を徹底
- [ ] バリデーションとエラーハンドリングの強化
- [ ] 全体の結合テスト
- [ ] APIドキュメントの更新
