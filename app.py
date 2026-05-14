from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector

app = Flask(__name__)
CORS(app)

# =========================
# DB CONNECTION
# =========================
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="bfp_system"
)

# =========================
# HOME
# =========================
@app.route("/")
def home():
    return "API RUNNING"


# =========================
# SUBMIT REPORT
# =========================
@app.route("/report", methods=["POST"])
def report():

    try:
        data = request.get_json()

        user_id = data.get("user_id")
        description = data.get("description")
        severity = data.get("severity")
        lat = data.get("lat")
        lng = data.get("lng")

        # IMPORTANT: type OR category (safe fallback)
        type_ = data.get("type") or "Unknown"
        category = data.get("category") or type_

        if not description or not severity:
            return jsonify({
                "success": False,
                "message": "Missing description or severity"
            }), 400

        cur = db.cursor()

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
        cur.close()

        return jsonify({
            "success": True,
            "message": "Report Saved"
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500


# =========================
# GET REPORTS (WITH NAME FIX)
# =========================
@app.route("/reports", methods=["GET"])
def get_reports():

    try:
        cur = db.cursor(dictionary=True)

        cur.execute("""
            SELECT 
                reports.*,
                users.full_name
            FROM reports
            LEFT JOIN users ON users.user_id = reports.user_id
            ORDER BY reports.id DESC
        """)

        rows = cur.fetchall()
        cur.close()

        return jsonify(rows)

    except Exception as e:
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500


# =========================
# UPDATE STATUS (CLEAN)
# =========================
@app.route("/update_status", methods=["POST"])
def update_status():

    try:
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
            return jsonify({
                "success": False,
                "message": "Invalid request"
            }), 400

        cur = db.cursor()

        cur.execute("""
            UPDATE reports
            SET status = %s
            WHERE id = %s
        """, (status, report_id))

        db.commit()

        # BFP QUEUE LOGIC
        if status == "CONFIRMED":

            cur.execute("""
                SELECT id FROM bfp_queue
                WHERE report_id = %s
            """, (report_id,))

            if not cur.fetchone():

                cur.execute("""
                    INSERT INTO bfp_queue (report_id, status)
                    VALUES (%s, %s)
                """, (report_id, "CONFIRMED"))

                db.commit()

        elif status == "REJECTED":

            cur.execute("""
                DELETE FROM bfp_queue
                WHERE report_id = %s
            """, (report_id,))

            db.commit()

        cur.close()

        return jsonify({
            "success": True,
            "message": f"Status updated to {status}"
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500


# =========================
# BFP QUEUE
# =========================
@app.route("/bfp_queue", methods=["GET"])
def bfp_queue():

    try:
        cur = db.cursor(dictionary=True)

        cur.execute("""
            SELECT
                bfp_queue.id,
                bfp_queue.status AS queue_status,
                reports.*
            FROM bfp_queue
            INNER JOIN reports ON bfp_queue.report_id = reports.id
            ORDER BY bfp_queue.id DESC
        """)

        rows = cur.fetchall()
        cur.close()

        return jsonify(rows)

    except Exception as e:
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500


# =========================
# RUN
# =========================
if __name__ == "__main__":
    app.run(
        host="0.0.0.0",
        port=5000,
        debug=True
    )