install:
	composer install
	npm install
	npm run build
	cp -n .env.example .env
	php artisan key:generate
	php artisan storage:link
	@echo ""
	@echo "╔══════════════════════════════════════════════════╗"
	@echo "║  Konfigurasi .env sebelum lanjut:                ║"
	@echo "║  • DB_HOST / DB_DATABASE / DB_USERNAME           ║"
	@echo "║  • MAIL_USERNAME / MAIL_PASSWORD (Gmail SMTP)    ║"
	@echo "║  • LOG_SLACK_WEBHOOK_URL (Slack notifikasi)      ║"
	@echo "║                                                  ║"
	@echo "║  Lalu jalankan: make setup                       ║"
	@echo "╚══════════════════════════════════════════════════╝"
	@echo ""

setup:
	php artisan migrate:fresh --seed

dev:
	composer run dev

test:
	php artisan test

report:
	rm -f storage/logs/integration-testing.log
	mkdir -p tests-report
	php artisan test --log-junit tests-report/junit.xml --testdox-html tests-report/testdox.html
	@echo ""
	@echo "Artefak laporan integration testing:"
	@echo "  - storage/logs/integration-testing.log (jejak langkah per ID)"
	@echo "  - tests-report/junit.xml (hasil eksekusi)"
	@echo "  - tests-report/testdox.html (ringkasan per skenario)"

queue:
	php artisan queue:listen --tries=3
