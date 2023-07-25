# Copyright VMware, Inc.
# SPDX-License-Identifier: APACHE-2.0

FROM docker.io/bitnami/symfony:6.2

EXPOSE 8000

WORKDIR /app

COPY . .
ENTRYPOINT [ "/opt/bitnami/scripts/symfony/entrypoint.sh" ]
CMD [ "/opt/bitnami/scripts/symfony/run.sh" ]