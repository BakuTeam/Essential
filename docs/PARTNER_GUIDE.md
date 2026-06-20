# Partner Guide

Этот документ описывает рабочий порядок для партнеров Dummy.

## Быстрый старт

```bash
git clone https://github.com/lunarixbe/dummy.git
cd dummy
composer install --no-dev --prefer-dist --optimize-autoloader
```

`vendor/` создается локально после `composer install`. В репозитории он не хранится.

## Ветки

- `main` - актуальная production-ветка ядра.
- `mcbe-1.1.5` - legacy-ядро с поддержкой Minecraft Bedrock `1.1.5` / protocol `113`.

Переключение на legacy:

```bash
git fetch origin
git checkout mcbe-1.1.5
composer install --no-dev --prefer-dist --optimize-autoloader
```

## Обновление

```bash
git fetch origin
git status
git pull --ff-only
composer install --no-dev --prefer-dist --optimize-autoloader
```

Если у вас есть локальные изменения, сначала перенесите их в отдельную ветку.

## Что не коммитить

- `vendor/`
- `.phar` и архивы сборок
- `plugins/`, `plugin_data/`, `worlds/`
- логи, crashdump-файлы и локальные конфиги сервера
- токены, пароли, приватные ключи и `.env`

## Баг-репорт

Для быстрой проверки приложите:

- ветку и commit hash;
- версию Minecraft Bedrock и protocol ID;
- список установленных плагинов;
- шаги воспроизведения;
- crashdump или релевантный фрагмент лога.

