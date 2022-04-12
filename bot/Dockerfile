FROM python:3.9-alpine

RUN apk add --no-cache bash

WORKDIR /app

COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

ENTRYPOINT ["python"]
CMD ["main.py"]
