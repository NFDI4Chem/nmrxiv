build-nginx:
	docker image build -f resources/ops/docker/nginx/Dockerfile -t nmrxiv-nginx:latest --target nginx .

build-fpm:
	docker image build -f resources/ops/docker/fpm/Dockerfile -t nmrxiv-fpm:latest --target fpm .