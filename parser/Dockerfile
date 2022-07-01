FROM python:3.10-alpine

RUN apk add --no-cache bash make automake gcc g++ subversion python3-dev libffi-dev

WORKDIR /app

COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt

RUN addgroup -g 1000 app && adduser -u 1000 -G app -s /bin/sh -D app

COPY . .
