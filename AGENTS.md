# Brisa PrestaShop

- This is a Spanish demonstration store, not a live business. Keep real payment providers and outbound mail disabled unless the user explicitly changes scope.
- Customize `themes/brisa` and the Brisa modules. Do not patch PrestaShop core or the parent Hummingbird theme.
- Catalog JSON and image generation are initial fixtures. Do not reseed, overwrite catalog changes, drop database tables or remove installation markers during an ordinary deploy.
- Credentials are in ignored `.env` / `.secrets` or Railway variables. Never print them in logs or commit them.
- The Railway project is `prueba-prestashop`, ID `ef9fdf0d-aa6e-4e55-9129-666726bd2806`. Services: `prestashop` and `MySQL`. Keep one application replica and preserve both volumes.
- GitHub remote: `amariner/prueba-prestashop`. Railway follows `main`.
- Verify shell syntax with `bash -n docker/entrypoint.sh`, PHP syntax in the container, and relevant Playwright flows with `npm test`. GitHub Actions builds and starts the real store.
- For checkout changes, test a fictitious order and verify its state is `Demo · Sin cobro`. Never use personal or payment information for tests.
- Report what was deployed, what was tested and any remaining limitation. Keep README deployment details current.
