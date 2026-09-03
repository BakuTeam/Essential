# Contributing to Essential

Thanks for helping improve Essential. Please keep changes focused and explain user-visible behaviour in the pull request description.

## Before opening a pull request

- Search existing issues and pull requests.
- Reproduce protocol changes against the affected Bedrock version.
- Run `composer validate` and the PHP checks locally when possible.
- Do not commit generated server data, logs, worlds, plugins, or `.phar` files.
- Preserve compatibility with supported Bedrock protocols unless a breaking change is explicitly documented.

Pull requests should target `main`, include a concise summary, testing details, and any relevant protocol number or packet-format reference.
