import React from "react";
import PropTypes from "prop-types";

const FormInput = ({
    label,
    name,
    type = "text",
    value,
    onChange,
    placeholder = "",
    error = null,
    required = false,
    disabled = false,
    options = [],
}) => {
    const renderInput = () => {
        switch (type) {
            case "textarea":
                return (
                    <textarea
                        id={name}
                        name={name}
                        value={value || ""}
                        onChange={onChange}
                        placeholder={placeholder}
                        required={required}
                        disabled={disabled}
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ${
                            error ? "border-red-500" : ""
                        }`}
                        rows={4}
                    />
                );
            case "select":
                return (
                    <select
                        id={name}
                        name={name}
                        value={value || ""}
                        onChange={onChange}
                        required={required}
                        disabled={disabled}
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ${
                            error ? "border-red-500" : ""
                        }`}
                    >
                        <option value="">-- Pilih {label} --</option>
                        {options.map((option) => (
                            <option key={option.value} value={option.value}>
                                {option.label}
                            </option>
                        ))}
                    </select>
                );
            case "file":
                return (
                    <div className="mt-1">
                        <input
                            id={name}
                            name={name}
                            type="file"
                            onChange={onChange}
                            required={required}
                            disabled={disabled}
                            className={`block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 ${
                                error ? "border-red-500" : ""
                            }`}
                            accept="image/*"
                        />
                        {value && typeof value === "string" && (
                            <div className="mt-2">
                                <p className="text-sm text-gray-500">File saat ini: {value}</p>
                            </div>
                        )}
                    </div>
                );
            case "checkbox":
                return (
                    <div className="mt-1">
                        <input
                            id={name}
                            name={name}
                            type="checkbox"
                            checked={value || false}
                            onChange={onChange}
                            required={required}
                            disabled={disabled}
                            className={`rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 ${
                                error ? "border-red-500" : ""
                            }`}
                        />
                    </div>
                );
            default:
                return (
                    <input
                        id={name}
                        name={name}
                        type={type}
                        value={value || ""}
                        onChange={onChange}
                        placeholder={placeholder}
                        required={required}
                        disabled={disabled}
                        className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ${
                            error ? "border-red-500" : ""
                        }`}
                    />
                );
        }
    };

    return (
        <div className="mb-4">
            <label htmlFor={name} className="block text-sm font-medium text-gray-700">
                {label}
                {required && <span className="text-red-500 ml-1">*</span>}
            </label>
            {renderInput()}
            {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
        </div>
    );
};

FormInput.propTypes = {
    label: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    type: PropTypes.string,
    value: PropTypes.any,
    onChange: PropTypes.func.isRequired,
    placeholder: PropTypes.string,
    error: PropTypes.string,
    required: PropTypes.bool,
    disabled: PropTypes.bool,
    options: PropTypes.arrayOf(
        PropTypes.shape({
            value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]).isRequired,
            label: PropTypes.string.isRequired,
        })
    ),
};

export default FormInput;