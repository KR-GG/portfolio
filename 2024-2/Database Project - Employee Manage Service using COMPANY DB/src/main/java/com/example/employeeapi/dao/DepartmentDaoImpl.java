package com.example.employeeapi.dao;

import com.example.employeeapi.exception.ConstraintViolationException;
import com.example.employeeapi.exception.ForeignKeyConstraintViolationException;
import com.example.employeeapi.exception.UniqueConstraintViolationException;
import com.example.employeeapi.model.Department;
import com.example.employeeapi.service.DepartmentService;
import org.springframework.dao.DataIntegrityViolationException;
import org.springframework.dao.EmptyResultDataAccessException;
import org.springframework.jdbc.core.BeanPropertyRowMapper;
import org.springframework.jdbc.core.namedparam.MapSqlParameterSource;
import org.springframework.jdbc.core.namedparam.NamedParameterJdbcTemplate;
import org.springframework.stereotype.Repository;

import javax.sql.DataSource;
import java.util.List;
import java.util.Map;

@Repository
public class DepartmentDaoImpl implements DepartmentDao{

    private final NamedParameterJdbcTemplate template;

    public DepartmentDaoImpl(DataSource dataSource) {
        this.template = new NamedParameterJdbcTemplate(dataSource);
    }

    @Override
    public Department getDepartmentByDnumber(int dnumber) {
        String sql = "select * from DEPARTMENT where dnumber = :dnumber and trash = false";

        MapSqlParameterSource params = new MapSqlParameterSource();
        params.addValue("dnumber", dnumber);

        try {
            return template.queryForObject(sql, params, new BeanPropertyRowMapper<>(Department.class));
        } catch (EmptyResultDataAccessException e) {
            return null;
        }
    }

    @Override
    public List<Department> getDepartmentByAttr(List<String> searchAttr, List<Object> departmentValue) {
        StringBuilder queryBuilder = new StringBuilder("select * from DEPARTMENT where ");

        MapSqlParameterSource params = new MapSqlParameterSource();

        for (int i = 0; i < searchAttr.size(); i++) {
            queryBuilder.append(searchAttr.get(i)).append(" = :param").append(i);
            params.addValue("param" + i, departmentValue.get(i));
            if (i < searchAttr.size() - 1) {
                queryBuilder.append(" AND ");
            }
        }

        String sql = queryBuilder.toString();

        return template.query(sql, params, new BeanPropertyRowMapper<>(Department.class));
    }

    @Override
    public boolean deleteDepartmentByDnumber(int dnumber) {
        String sql = "DELETE FROM DEPARTMENT WHERE dnumber = :dnumber AND trash = true";
        MapSqlParameterSource params = new MapSqlParameterSource();
        params.addValue("dnumber", dnumber);

        try {
            int result = template.update(sql, params);
            return result > 0;
        } catch (DataIntegrityViolationException e) {
            if (e.getMessage().contains("FOREIGN KEY")) {
                throw new ForeignKeyConstraintViolationException("Foreign key constraint violation: " + e.getMessage());
            }
            else if (e.getMessage().contains("UNIQUE")) {
                throw new UniqueConstraintViolationException("Unique constraint violation: " + e.getMessage());
            }
            else {
                throw new ConstraintViolationException("Constraint violation: " + e.getMessage());
            }
        } catch (Exception e) {
            e.printStackTrace();
            return false;
        }
    }

    @Override
    public Department updateDepartmentByDnumber(int dnumber, Map<String, Object> updateValue) {
        StringBuilder sql = new StringBuilder("UPDATE DEPARTMENT SET ");

        MapSqlParameterSource params = new MapSqlParameterSource();

        for (Map.Entry<String, Object> entry: updateValue.entrySet()) {
            sql.append(entry.getKey()).append(" = :").append(entry.getKey()).append(", ");
            params.addValue(entry.getKey(), entry.getValue());
        }

        sql.setLength(sql.length() -2);
        sql.append(" WHERE Dnumber = :dnumber AND trash = false");
        params.addValue("dnumber", dnumber);

        try {
            int result = template.update(sql.toString(), params);
            if (result > 0) {
                return getDepartmentByDnumber(dnumber);
            }
            else return null;
        } catch (DataIntegrityViolationException e) {
            if (e.getMessage().contains("FOREIGN KEY")) {
                throw new ForeignKeyConstraintViolationException("Foreign key constraint violation: " + e.getMessage());
            }
            else if (e.getMessage().contains("UNIQUE")) {
                throw new UniqueConstraintViolationException("Unique constraint violation: " + e.getMessage());
            }
            else {
                throw new ConstraintViolationException("Constraint violation: " + e.getMessage());
            }
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    @Override
    public Department restoreDepartmentByDnumber(int dnumber) {
        String sql = "UPDATE DEPARTMENT SET trash = false WHERE Dnumber = :dnumber AND trash = true";
        MapSqlParameterSource params = new MapSqlParameterSource();
        params.addValue("dnumber", dnumber);

        try {
            int result = template.update(sql, params);
            if (result > 0) {
                return getDepartmentByDnumber(dnumber);
            }
            else return null;
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    @Override
    public Department addDepartment(List<Object> addingValue) {
        if (addingValue.size() != 4) {
            throw new IllegalArgumentException("Invalid changeValue list");
        }

        String sql = "INSERT INTO DEPARTMENT (Dname, Dnumber, Mgr_ssn, Mgr_start_date, trash) VALUES (:Dname, :Dnumber, :Mgr_ssn, :Mgr_start_date, :trash)";

        MapSqlParameterSource params = new MapSqlParameterSource();
        params.addValue("Dname", addingValue.get(0));
        params.addValue("Dnumber", Integer.parseInt(addingValue.get(1).toString()));
        params.addValue("Mgr_ssn", addingValue.get(2));
        params.addValue("Mgr_start_date", addingValue.get(3));
        params.addValue("trash", false);

        try {
            int result = template.update(sql, params);
            if (result > 0) {
                return getDepartmentByDnumber(Integer.parseInt(addingValue.get(1).toString()));
            }
            else return null;
        } catch (DataIntegrityViolationException e) {
            if (e.getMessage().contains("FOREIGN KEY")) {
                throw new ForeignKeyConstraintViolationException("Foreign key constraint violation: " + e.getMessage());
            }
            else if (e.getMessage().contains("UNIQUE")) {
                throw new UniqueConstraintViolationException("Unique constraint violation: " + e.getMessage());
            }
            else {
                throw new ConstraintViolationException("Constraint violation: " + e.getMessage());
            }
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    @Override
    public Double getDepartmentInfo(int dnumber, DepartmentService.OperationType operationType) {
        String sql = "SELECT " + operationType + "(Salary) FROM EMPLOYEE, DEPARTMENT WHERE Dno = Dnumber AND Dnumber = :dnumber AND EMPLOYEE.trash = false";

        MapSqlParameterSource params = new MapSqlParameterSource();
        params.addValue("dnumber", dnumber);

        try {
            return template.queryForObject(sql, params, Double.class);
        } catch (Exception e) {
            System.err.println("Error fetching department info: " + e.getMessage());
            return null;
        }
    }
}
