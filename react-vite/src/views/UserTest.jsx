import React, { useEffect, useState } from 'react'
import { useLocation, useNavigate, useParams, useSearchParams } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import { 
  Box,
  Button,
  Select,
  MenuItem,
  FormControl,
  InputLabel,
  CircularProgress,
  TextField
} from '@mui/material'

import { useForm, Controller } from 'react-hook-form'
import { useDisplayContext } from '../contexts/DisplayContext'
import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";

const UserTest = () => {


    const [userID, setUserID] = useState(null);


    const { setNotification} = useStateContext()
    const { filiales, getFiliales, roles, getRoles, loading, setLoading
    , getAdmissions, admissions } = useDisplayContext();
    const navigate = useNavigate();
    const location = useLocation();

    // const {id} = useParams();


    const useSchema = yup.object({
        name: yup.string().required("veuillez entrer le nom d'utilisateur"),
        email: yup.string().required("veuillez entrer l'email de l'utilisateur"),
        password: yup.string().required(),
        password_confirmation: yup.string().required(),
        filiale: yup.number().required(),
        role: yup.string().required(),
        admission: yup.number().required(),
    })

    const { handleSubmit, control, setError, register, formState, formState: { errors } } = useForm({
        mode: "onChange",
        resolver: yupResolver(useSchema)
    });
    
    const [user, setUser] = useState({
        id: null,
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: '',
        filiale: null,
        admission: null,
    })

    useEffect(() => {
        getRoles();
        getFiliales();
        getAdmissions();
        console.log(admissions)
    }, [])


    const onSubmit = (data) => {
        const dataUser = {
            name: data.name,
            email: data.email,
            password: data.password,
            password_confirmation: data.password_confirmation,
        }

        const dataRole = {
            role: data.role,
        }
        const dataFilial = {
            filiale: data.filiale,
        }
        const dataAdmission = {
            admission: data.admission,
        }

        axiosClient.post('/users', data).then((response) => {
            setNotification("utilisateur ajouté avec succès")
            navigate('/users')
        }).catch(err => {
            const response = err.response;
            setError('server', {
                message: response.data.errors
            })
            console.log(err)
        })
        console.log(data);
    }

    return (
        <div className="card animated fadeInDown">
            <Box m="20px" p="20px">
                <form onSubmit={handleSubmit(onSubmit)}>
                    <TextField
                        fullWidth
                        label="name"
                        sx={{ marginBottom: "20px" }}
                        {...register("name")}
                        onChange={ev => setUser({...user, name: ev.target.value})}
                        value={user.name}
                        variant="outlined"
                        error={errors.name ? true : false}
                        helperText={errors.name ? 
                            errors.name?.message :
                            "entrer le nom de l'utilisateur"
                        }                   
                    />
                    <TextField
                        fullWidth
                        label="email"
                        sx={{ marginBottom: "20px" }}
                        {...register("email")}
                        onChange={ev => setUser({...user, email: ev.target.value})}
                        value={user.email}
                        variant="outlined"
                        error={errors.email ? true : false}
                        helperText={errors.email ? 
                            errors.email?.message :
                            "entrer le mail de l'utilisateur"
                        }
                    />
                    <TextField
                        fullWidth
                        type='password'
                        label="password"
                        sx={{ marginBottom: "20px" }}
                        {...register("password")}
                        onChange={ev => setUser({...user, password: ev.target.value})}
                        value={user.password}
                        variant="outlined"
                        error={errors.password ? true : false}
                        helperText={errors.password ? 
                            errors.password?.message :
                            "entrer le mots de passe de l'utilisateur"
                        }
                    />
                    <TextField
                        fullWidth
                        type='password'
                        label="Password Confirmation"
                        sx={{ marginBottom: "20px" }}
                        {...register("password_confirmation")}
                        onChange={ev => setUser({...user, password_confirmation: ev.target.value})}
                        value={user.password_confirmation}
                        variant="outlined"
                        error={errors.password_confirmation ? true : false}
                        helperText={errors.password_confirmation ? 
                            errors.password_confirmation?.message :
                            "entrer la confirmation du mots de passe"
                        }                    
                    />   
                    <Box marginBottom="20px">
                        <label htmlFor=""
                            style={{ position: "relative", top: "15px", fontWeight: "500", color: "rgba(0, 0, 0, 0.6)"}}
                        > Entrer la Filiale de l'utilisateur : </label>
                        <Controller
                        control={control}
                        name="filiale"
                        render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                            <InputLabel id="demo-simple-select-label"> access filiale  </InputLabel>
                            <Select
                                label="filiale access"
                                value={user.filiale} 
                                {...register("filiale")}
                                onChange={ev => setUser({...user, filiale: ev.target.value})}
                                error={errors.filiale ? true : false}
                                >
                                {(filiales !== null && filiales !== undefined && (Object.keys(filiales).length !== 0)) 
                                && filiales?.map(filiale => (
                                <MenuItem value={filiale.id} key={filiale.id}> {filiale.name} </MenuItem>
                                ))}
                            </Select>
                            {errors.filiale && <span style={{
                                        color: "#d32f2f",
                                        fontSize: "0.75em",
                                        textAlign: "left",
                                        fontWeight: "400"
                            }}> {errors.filiale?.message} </span>}
                            </FormControl>)}
                        />
                    </Box>
   
                    <Box marginBottom="20px">
                        <label htmlFor=""
                            style={{ position: "relative", top: "15px", fontWeight: "500", color: "rgba(0, 0, 0, 0.6)"}}
                        > Entrer le Role de l'utilisateur : </label>
                        <Controller
                            control={control}
                            name="role"
                            render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                                <InputLabel id="demo-simple-select-label"> access role  </InputLabel>
                                <Select
                                label="role access"
                                value={user.role}
                                {...register("role")}
                                onChange={ev => setUser({...user, role: ev.target.value})}
                                error={errors.role ? true : false}
                                >
                                {(roles !== null && roles !== undefined) && roles?.map(role => (
                                    <MenuItem value={role.name} key={role.id}> {role.name} </MenuItem>
                                ))}
                                </Select>
                                {errors.role && <span style={{
                                        color: "#d32f2f",
                                        fontSize: "0.75em",
                                        textAlign: "left",
                                        fontWeight: "400"
                                }}> {errors.role?.message} </span>}
                            </FormControl>
                            )}
                        />   
                    </Box>
                    <Box marginBottom="20px">
                    <label htmlFor=""
                            style={{ position: "relative", top: "15px", fontWeight: "500", color: "rgba(0, 0, 0, 0.6)"}}
                        > Entrer l'admission de l'utilisateur : </label>
                        <Controller
                        control={control}
                        name="admission"
                        render={({ field: { onChange } }) => (
                            <FormControl variant="outlined" sx={{ width: "300px" }}>
                            <InputLabel id="demo-simple-select-label"> access admission  </InputLabel>
                            <Select
                                label="admission access"
                                value={user.admission} 
                                {...register("admission")}
                                onChange={ev => setUser({...user, admission: ev.target.value})}
                                error={errors.admission ? true : false}
                                >
                                {(admissions !== null && admissions !== undefined && (Object.keys(admissions).length !== 0)) 
                                && admissions?.map(admission => (
                                <MenuItem value={admission.id} key={admission.id}> {admission.table} </MenuItem>
                                ))}
                            </Select>
                            {errors.admission && <span style={{
                                        color: "#d32f2f",
                                        fontSize: "0.75em",
                                        textAlign: "left",
                                        fontWeight: "400"
                            }}> {errors.admission?.message} </span>}
                            </FormControl>)}
                        /> 
                    </Box>
                    <Box display="flex" justifyContent="end" mt="20px">
                        <Button  type="submit" color="primary" variant="contained">
                            Add Filiale user
                        </Button>
                    </Box>
              
                </form>
            </Box>
        </div>
    );
};

export default UserTest;