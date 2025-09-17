import React, { useState, useEffect } from "react";
import axios from "axios";
import qs from "qs";

function Kyc() {
  const [kycData, setKycData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState(null);

  useEffect(() => {
    fetchKycData();
  }, []);

  const fetchKycData = () => {
    const requestData = {
      action: "wps_get_kyc_requests",
      nonce: frontend_ajax_object.wps_standard_nonce,
    };

    axios
      .post(frontend_ajax_object.ajaxurl, qs.stringify(requestData))
      .then((res) => {
        if (res.data && res.data.success) {
          const withRemarks = res.data.data.map((row) => ({
            ...row,
            remark: row.remark || "",
          }));
          setKycData(withRemarks);
        } else {
          setKycData([]);
        }
        setLoading(false);
      })
      .catch((err) => {
        console.error("Error fetching KYC data:", err);
        setLoading(false);
      });
  };

  const handleStatusChange = (userId, newStatus, remark) => {
    if (!remark || remark.trim() === "") {
      setMessage("⚠️ Please enter a remark before changing status.");
      setTimeout(() => setMessage(null), 3000);
      return;
    }

    const requestData = {
      action: "wps_update_kyc_status",
      nonce: frontend_ajax_object.wps_standard_nonce,
      user_id: userId,
      status: newStatus,
      remark: remark,
    };

    axios
      .post(frontend_ajax_object.ajaxurl, qs.stringify(requestData))
      .then((res) => {
        if (res.data && res.data.success) {
          setKycData((prevData) =>
            prevData.map((row) =>
              row.id === userId
                ? { ...row, status: newStatus, remark: remark }
                : row
            )
          );
          setMessage(`✅ Status updated to "${newStatus}" with remark.`);
        } else {
          setMessage("❌ " + (res.data?.message || "Failed to update status."));
        }
        setTimeout(() => setMessage(null), 3000);
      })
      .catch((err) => {
        console.error("Error updating status:", err);
        setMessage("❌ Error updating status.");
        setTimeout(() => setMessage(null), 3000);
      });
  };

  const handleRemarkChange = (userId, value) => {
    setKycData((prevData) =>
      prevData.map((row) =>
        row.id === userId ? { ...row, remark: value } : row
      )
    );
  };

  return (
    <div className="kyc-container">
      {message && <div className="kyc-message">{message}</div>}

      {loading ? (
        <p className="kyc-loading">Loading...</p>
      ) : (
        <table className="kyc-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Required Document</th>
              <th>Remark</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            {kycData.length > 0 ? (
              kycData.map((row) => {
                const isFinal =
                  row.status === "approved" || row.status === "rejected";
                return (
                  <tr key={row.id}>
                    <td>{row.id}</td>
                    <td>{row.name}</td>
                    <td>{row.email}</td>
                    <td>
                      {Array.isArray(row.required_document) &&
                        row.required_document.map((doc, index) => (
                          <a
                            key={index}
                            href={doc}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="kyc-document-link"
                          >
                            Document {index + 1}
                          </a>
                        ))}
                    </td>
                    <td>
                      <input
                        type="text"
                        value={row.remark}
                        onChange={(e) =>
                          handleRemarkChange(row.id, e.target.value)
                        }
                        placeholder="Enter remark"
                        className="kyc-remark-input"
                        disabled={isFinal}
                      />
                    </td>
                    <td>
                      <select
                        value={row.status}
                        onChange={(e) =>
                          handleStatusChange(row.id, e.target.value, row.remark)
                        }
                        className="kyc-status-select"
                        disabled={isFinal}
                      >
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                      </select>
                    </td>
                  </tr>
                );
              })
            ) : (
              <tr>
                <td colSpan="6" className="kyc-empty">
                  No KYC requests found.
                </td>
              </tr>
            )}
          </tbody>
        </table>
      )}
    </div>
  );
}

export default Kyc;
