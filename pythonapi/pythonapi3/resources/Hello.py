from flask_restful import Resource

class Hello(Resource):
    def get(self):
        return {"message": "get"}

    def post(self):
        return {"message": "Post"}    