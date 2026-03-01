FROM node:20-alpine

WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm install

EXPOSE 3000

CMD [ "npm", "run", "dev" ]