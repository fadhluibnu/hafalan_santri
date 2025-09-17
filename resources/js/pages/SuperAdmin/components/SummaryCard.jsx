import React from "react";
import PropTypes from "prop-types";

const SummaryCard = ({ title, count, icon, color }) => {
    // Default colors if not specified
    const bgColors = {
        blue: "bg-blue-100 text-blue-800",
        green: "bg-green-100 text-green-800",
        orange: "bg-orange-100 text-orange-800",
        purple: "bg-purple-100 text-purple-800",
    };
    
    const bgColor = bgColors[color] || bgColors.blue;
    
    return (
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
            <div className="p-5">
                <div className="flex items-center justify-between">
                    <div>
                        <h3 className="text-lg font-semibold text-gray-700">{title}</h3>
                        <p className="text-3xl font-bold mt-2">{count}</p>
                    </div>
                    <div className={`p-3 rounded-full ${bgColor}`}>
                        {icon}
                    </div>
                </div>
            </div>
        </div>
    );
};

SummaryCard.propTypes = {
    title: PropTypes.string.isRequired,
    count: PropTypes.oneOfType([PropTypes.string, PropTypes.number]).isRequired,
    icon: PropTypes.element,
    color: PropTypes.string,
};

export default SummaryCard;