FROM python:3.10-alpine

RUN apk add --no-cache bash make automake gcc g++ subversion python3-dev libffi-dev

WORKDIR /app

COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt

COPY . .
