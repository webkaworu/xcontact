# 開発計画＆進捗トラッカー

このドキュメントは、問い合わせフォームバックエンドシステムの開発計画とタスクの進捗を管理します。

---

### フェーズ1: プロジェクトセットアップ

- [x] `docs` ディレクトリの作成とドキュメント設置
- [x] データベース接続情報の設定 (`.env`)
- [ ] **【ブロック中】** 必要なPHP拡張機能のインストール (`pdo_mysql`, `pdo_sqlite`)

---

### フェーズ2: 認証・認可機能の実装

- [ ] Laravel Sanctum または Breeze のインストールと設定 (API認証のため)
- [ ] **データベースマイグレーション**
    - [ ] `users` テーブル修正 (`role`カラム, `form_creation_limit`カラムの追加)
    - [ ] `permissions` テーブルのマイグレーション作成
    - [ ] `permission_user` テーブルのマイグレーション作成
    - [ ] `forms` テーブル修正 (`user_id` カラムの追加)
- [ ] **APIエンドポイント実装**
    - [ ] `/register` (ユーザー登録)
    - [ ] `/login` (ログイン)
    - [ ] `/logout` (ログアウト)
    - [ ] `/api/users` (ユーザー一覧取得 - 管理者)
    - [ ] `/api/users/{id}` (ユーザー情報更新 - 管理者)
    - [ ] `/api/users/{id}/permissions` (権限管理 - 管理者)
- [ ] **認可ロジックの実装**
    - [ ] Gate/Policy を使用して各APIエンドポイントを保護

---

### フェーズ3: コア機能のモデルとマイグレーション

- [ ] `Form` モデルとマイグレーションファイルの作成
- [ ] `Inquiry` モデルとマイグレーションファイルの作成
- [ ] `EmailTemplate` モデルとマイグレーションファイルの作成
- [ ] `AccessToken` モデル (旧`SubmissionToken`) とマイグレーションファイルの作成
- [ ] **マイグレーションファイルの修正 (流量制限・権限対応)**
    - [ ] `forms` テーブルに `daily_limit`, `monthly_limit` カラムを追加
    - [ ] `access_tokens` テーブルのスキーマを修正
- [ ] **【ブロック中】** 作成・修正した全マイグレーションを実行してテーブルを構築

---

### フェーズ4: APIエンドポイントの実装 (コア機能)

- [ ] **フォーム管理 (CRUD)** (認可チェック込み)
    - [ ] `POST /api/forms` (作成数上限チェック込み)
    - [ ] `GET /api/forms`
    - [ ] `GET /api/forms/{id}`, `PUT /api/forms/{id}`, `DELETE /api/forms/{id}`
- [ ] **メールテンプレート管理 (CRUD)** (管理者のみ)
    - [ ] `GET /api/templates`, `POST /api/templates`
    - [ ] `GET /api/templates/{id}`, `PUT /api/templates/{id}`, `DELETE /api/templates/{id}`
- [ ] **流量制限管理** (認可チェック込み)
    - [ ] `PUT /api/forms/{id}/rate-limits`
- [ ] **問い合わせフロー**
    - [ ] `GET /api/forms/{form_id}/generate-token`
    - [ ] `POST /submit/{token}` (流量制限チェック込み)
- [ ] **問い合わせ履歴** (認可チェック込み)
    - [ ] `GET /api/forms/{form_id}/inquiries`

---

### フェーズ5: テストと改善

- [ ] 認証・認可機能に関するテスト
- [ ] 各APIエンドポイントのFeatureテスト (権限ごと)
- [ ] 流量制限・作成数制限機能のテスト
- [ ] バリデーションルールの実装

---

### フェーズ6: ドキュメント

- [x] 要件定義書 (`REQUIREMENTS.md`) の作成
- [x] 開発計画書 (`DEVELOPMENT_PLAN.md`) の作成
- [ ] APIの利用方法に関するドキュメントを更新
