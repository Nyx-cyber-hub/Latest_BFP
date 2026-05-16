from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector
import pandas as pd
from sklearn.tree import DecisionTreeClassifier
import os
import base64
import time

app = Flask(__name__)

# =========================
# CORS (ALLOW FRONTEND)
# =========================
CORS(app, resources={r"/*": {"origins": "*"}})

# =========================
# DATABASE CONNECTION
# =========================
def get_db():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="bfp_system",
        autocommit=False
    )

# =========================
# ML MODEL (RISK PREDICTION)
# =========================
training_data = {
    'severity': [1, 2, 3, 1, 3, 2, 3, 1],
    'incident_count': [2, 5, 10, 1, 15, 6, 12, 2],
    'risk': [0, 0, 1, 0, 1, 0, 1, 0]
}

df = pd.DataFrame(training_data)
X = df[['severity', 'incident_count']]
y = df['risk']

model = DecisionTreeClassifier()
model.fit(X, y)

# =========================
# HOME
# =========================
@app.route("/")
def home():
    return "API RUNNING"

# =========================
# FIXED REPORT ENDPOINT (JSON + FORM + CAMERA)
# =========================
@app.route("/report", methods=["POST"])
def report():
    db = None
    cur = None

    try:
        db = get_db()
        cur = db.cursor()

        # =========================
        # SUPPORT JSON OR FORM DATA
        # =========================
        if request.is_json:
            data = request.get_json()
        else:
            data = request.form

        user_id = data.get("user_id")
        description = data.get("description")
        severity = data.get("severity")
        lat = data.get("lat")
        lng = data.get("lng")

        type_ = data.get("type") or "Unknown"
        category = data.get("category") or type_

        # =========================
        # CAMERA IMAGE HANDLING
        # =========================
        photo_base64 = data.get("photo_base64")
        image_path = None

        if photo_base64:
            try:
                os.makedirs("uploads", exist_ok=True)

                header, encoded = photo_base64.split(",", 1)
                image_data = base64.b64decode(encoded)

                filename = f"report_{int(time.time())}.png"
                image_path = os.path.join("uploads", filename)

                with open(image_path, "wb") as f:
                    f.write(image_data)

            except Exception as e:
                return jsonify({
                    "success": False,
                    "message": "Image upload failed",
                    "error": str(e)
                }), 500

        # =========================
        # VALIDATION
        # =========================
        if not description or not severity:
            return jsonify({
                "success": False,
                "message": "Missing description or severity"
            }), 400

        # =========================
        # INSERT INTO DATABASE
        # =========================
        cur.execute("""
            INSERT INTO reports
            (user_id, description, severity, lat, lng, status, type, category)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """, (
            user_id,
            description,
            severity,
            lat,
            lng,
            "Pending",
            type_,
            category
        ))

        db.commit()

        return jsonify({
            "success": True,
            "message": "Report Saved Successfully",
            "image_saved": image_path
        })

    except Exception as e:
        if db:
            db.rollback()
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500

    finally:
        if cur:
            cur.close()
        if db:
            db.close()

# =========================
# GET REPORTS
# =========================
@app.route("/reports", methods=["GET"])
def get_reports():
    db = None
    cur = None

    try:
        db = get_db()
        cur = db.cursor(dictionary=True)

        cur.execute("""
            SELECT reports.*, users.full_name
            FROM reports
            LEFT JOIN users ON users.user_id = reports.user_id
            ORDER BY reports.id DESC
        """)

        return jsonify(cur.fetchall())

    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

    finally:
        if cur:
            cur.close()
        if db:
            db.close()

# =========================
# UPDATE STATUS
# =========================
@app.route("/update_status", methods=["POST"])
def update_status():
    db = None
    cur = None

    try:
        db = get_db()
        cur = db.cursor()

        data = request.get_json()

        report_id = data.get("id")
        status = data.get("status")

        map_status = {
            "Confirmed": "CONFIRMED",
            "Rejected": "REJECTED",
            "CONFIRMED": "CONFIRMED",
            "REJECTED": "REJECTED"
        }

        status = map_status.get(status)

        if not report_id or not status:
            return jsonify({"success": False, "message": "Invalid request"}), 400

        cur.execute("""
            UPDATE reports
            SET status = %s
            WHERE id = %s
        """, (status, report_id))

        db.commit()

        return jsonify({
            "success": True,
            "message": f"Status updated to {status}"
        })

    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

    finally:
        if cur:
            cur.close()
        if db:
            db.close()

# =========================
# RISK PREDICTION (ML)
# =========================
@app.route("/predict_risk", methods=["POST"])
def predict_risk():
    try:
        data = request.get_json()

        severity = int(data.get("severity"))
        incident_count = int(data.get("incident_count"))

        prediction = model.predict([[severity, incident_count]])

        return jsonify({
            "success": True,
            "prediction": "HIGH RISK" if prediction[0] == 1 else "LOW RISK"
        })

    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

# =========================
# SEND MESSAGE
# =========================
@app.route("/send_message", methods=["POST"])
def send_message():
    db = None
    cur = None

    try:
        db = get_db()
        cur = db.cursor()

        data = request.get_json()

        sender = data.get("sender")
        receiver = data.get("receiver")
        message = data.get("message")

        if not sender or not receiver or not message:
            return jsonify({"success": False, "message": "Missing fields"}), 400

        cur.execute("""
            INSERT INTO messages (sender, receiver, message)
            VALUES (%s, %s, %s)
        """, (sender, receiver, message))

        db.commit()

        return jsonify({"success": True, "message": "Message Sent"})

    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

    finally:
        if cur:
            cur.close()
        if db:
            db.close()

# =========================
# GET MESSAGES
# =========================
@app.route("/messages", methods=["GET"])
def get_messages():
    db = None
    cur = None

    try:
        db = get_db()
        cur = db.cursor(dictionary=True)

        cur.execute("SELECT * FROM messages ORDER BY created_at DESC")

        return jsonify(cur.fetchall())

    except Exception as e:
        return jsonify({"success": False, "message": str(e)}), 500

    finally:
        if cur:
            cur.close()
        if db:
            db.close()

# =========================
# RUN SERVER
# =========================
if __name__ == "__main__":
    os.makedirs("uploads", exist_ok=True)
    app.run(host="0.0.0.0", port=5000, debug=True)