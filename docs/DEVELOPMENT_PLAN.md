# 開発計画＆進捗トラッカー

このドキュメントは、問い合わせフォームバックエンドシステムの開発計画とタスクの進捗を管理します。

---

### フェーズ1: プロジェクト基盤構築

- [x] `docs` ディレクトリと基本ドキュメント設置
- [ ] Laravel Sanctum のインストールと設定
- [ ] **データベースマイグレーション作成**
    - [ ] `users` テーブルへのカラム追加 (`form_creation_limit`, `registration_token_id`)
    - [ ] `roles`, `permissions` テーブル作成
    - [ ] `role_user`, `permission_role` 中間テーブル作成
    - [ ] `registration_tokens` テーブル作成
    - [ ] `forms`, `inquiries`, `email_templates`, `access_tokens` テーブル作成
- [ ] **マイグレーションファイルの編集**
    - [ ] 全テーブルのカラム定義を要件定義書に合わせて修正
- [ ] **モデルの作成とリレーション定義**
    - [ ] 全モデルの `fillable` プロパティ設定
    - [ ] モデル間のリレーション (`hasMany`, `belongsToMany` 等) を定義
- [ ] **シーダーの作成**
    - [ ] `RolesAndPermissionsSeeder` (デフォルトの役割と権限を作成)
    - [ ] `EmailTemplateSeeder` (デフォルトのメールテンプレートを作成)
    - [ ] `DatabaseSeeder` で上記シーダーを呼び出し
- [ ] `php artisan migrate --seed` を実行してDBを構築

---

### フェーズ2: 認証・登録機能の実装

- [ ] **APIエンドポイント実装**
    - [ ] `POST /register` (登録トークン検証ロジック込み)
    - [ ] `POST /login` (Sanctumのトークン発行)
    - [ ] `POST /logout`
- [ ] **登録トークン管理 (CRUD)**
    - [ ] `GET, POST /api/registration-tokens`
    - [ ] `DELETE /api/registration-tokens/{id}`
- [ ] **テスト**
    - [ ] ユーザー登録、ログイン、ログアウトのFeatureテスト

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

### フェーズ5: 仕上げ

- [ ] APIレスポンス形式の統一を徹底
- [ ] バリデーションとエラーハンドリングの強化
- [ ] 全体の結合テスト
- [ ] APIドキュメントの更新