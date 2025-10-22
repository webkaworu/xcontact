# Laravelプロジェクトにおけるクリーンアーキテクチャのフォルダ構成

このドキュメントでは、Laravelプロジェクトでクリーンアーキテクチャを実現するためのフォルダ構成を説明します。

## フォルダ構成
app/
├── Core/
│   ├── Domain/                # ドメイン層 (エンティティ、値オブジェクト、ドメインサービス)
│   │   ├── Entities/          # ドメインエンティティ
│   │   ├── ValueObjects/      # 値オブジェクト
│   │   └── Services/          # ドメインサービス
│   ├── Application/           # アプリケーション層 (ユースケース、DTO)
│   │   ├── UseCases/          # ユースケース
│   │   └── DTOs/              # データ転送オブジェクト
│   └── Contracts/             # インターフェース (リポジトリ、サービス)
│       ├── Repositories/      # リポジトリインターフェース
│       └── Services/          # サービスインターフェース
├── Infrastructure/            # インフラ層 (データベース、外部サービス)
│   ├── Persistence/
│   │   ├── Eloquent/          # Eloquentモデルやリポジトリ実装
│   │   └── Migrations/        # マイグレーションファイル
│   ├── Services/              # 外部サービスの実装 (例: メール、課金)
│   └── Providers/             # Laravelサービスプロバイダ
├── Interfaces/                # インターフェース層 (コントローラ、リクエスト、レスポンス)
│   ├── Http/
│   │   ├── Controllers/       # コントローラ
│   │   ├── Requests/          # リクエストバリデーション
│   │   └── Resources/         # APIリソース
│   └── CLI/                   # CLIコマンド
└── Exceptions/                # カスタム例外

## フォルダの役割

### Core/Domain
- アプリケーションのビジネスルールを表現します。
- エンティティ、値オブジェクト、ドメインサービスを含みます。

### Core/Application
- アプリケーションのユースケースを定義します。
- データ交換のためにDTO（データ転送オブジェクト）を使用します。

### Core/Contracts
- リポジトリやサービスのインターフェースを定義します。
- インフラ層への依存を避けるために設計されています。

### Infrastructure
- データベースや外部サービスとのやり取りを実装します。
- Eloquentモデルやリポジトリの実装を含みます。

### Interfaces
- ユーザーや外部システムとのやり取りを処理します。
- コントローラ、リクエスト、レスポンスを含みます。

### Exceptions
- アプリケーション全体で使用されるカスタム例外を定義します。

## Laravelの機能に関する注意点
- **サービスプロバイダ**: 依存性注入のために`Infrastructure/Providers`に配置します。
- **Eloquentモデル**: 必要に応じて`Infrastructure/Persistence/Eloquent`に配置します。
- **リクエストバリデーション**: フォームリクエストを`Interfaces/Http/Requests`に配置します。

この構成は、クリーンアーキテクチャの原則を遵守しつつ、Laravelの機能を活用することを目的としています。
