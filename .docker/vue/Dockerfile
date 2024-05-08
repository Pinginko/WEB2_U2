FROM node:latest as builder

WORKDIR /app

COPY frontendVue /app

RUN npm install
RUN npm run build

FROM nginx:latest

COPY --from=builder /app/dist /usr/share/nginx/html

EXPOSE 7070