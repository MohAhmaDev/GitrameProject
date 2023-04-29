import { Box, Button, TextField } from '@mui/material';
import React from 'react';
import { useForm, Controller } from "react-hook-form";


import { yupResolver } from '@hookform/resolvers/yup';
import * as yup from "yup";
import { useNavigate } from 'react-router-dom';
import { useStateContext } from '../contexts/ContextProvider';
import axiosClient from '../axios-client';






const EntreprisesForm = () => {

    const {setNotification} = useStateContext()
    const navigate = useNavigate();
    const phoneRegExp = /^((\+[1-9]{1,4}[ -]?)|(\([0-9]{2,3}\)[ -]?)|([0-9]{2,4})[ -]?)*?[0-9]{3,4}[ -]?[0-9]{3,4}$/;

    const userScheme = yup.object({
        nom_entreprise: yup.string().required(),
        groupe: yup.string().required(),
        adresse: yup.string().required(),
        secteur: yup.string().required(),
        nationalite: yup.string().required(),
        num_tel_entr: yup
        .string()
        .matches(phoneRegExp, "Phone number is not valid")
        .required("required"),
        adress_emil_entr: yup.string().email("invalid email").required("required"),
        status_juridique: yup.string().required(),
    })
    
    const { handleSubmit, control, 
        register, getValues, watch , setValue, reset, formState, setError, formState: { errors } } = useForm({
            mode: "onChange",
            resolver: yupResolver(userScheme)
    });
    const {isSubmitting, isValid, isSubmitted, isSubmitSuccessful} = formState    


    const onSubmit = (data) => {
        const entreprise = {
            nom_entreprise: data.nom_entreprise,
            groupe: data.groupe,
            adresse: data.adresse,
            secteur: data.secteur,
            nationalite: data.nationalite,
            num_tel_entr: data.num_tel_entr,
            adress_emil_entr: data.adress_emil_entr,
            status_juridique: data.status_juridique,
          }
          axiosClient.post('/entreprise', entreprise)
          .then(() => {
            setNotification("l'entreprise à bien été saisite")
            navigate('/')
          })
          .catch((err) => {
            console.log(err)
            const response = err.response;
            if (response && response.status === 422) {
              setError('server', {
                message: response.response.data.errors
              })       
            }
          })
    }


    return (
        <>
            <div className="card animated fadeInDown">
                <Box m="20px">
                    <form onSubmit={handleSubmit(onSubmit)}>
                        <Box display="grid" gap="30px" gridTemplateColumns="repeat(6, minmax(0, 1fr))">
                            <TextField
                                label="nom entreprise"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('nom_entreprise')}
                                error={errors?.nom_entreprise ? true : false}
                                helperText={errors?.nom_entreprise && errors?.nom_entreprise?.message}
                            />  
                            <TextField
                                label="groupe"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('groupe')}
                                error={errors?.groupe ? true : false}
                                helperText={errors?.groupe && errors?.groupe?.message}
                            /> 
                            <TextField
                                label="secteur"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('secteur')}
                                error={errors?.secteur ? true : false}
                                helperText={errors?.secteur && errors?.secteur?.message} 
                            /> 
                            <TextField
                                label="nationalite"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('nationalite')}
                                error={errors?.nationalite ? true : false}
                                helperText={errors?.nationalite && errors?.nationalite?.message}
                            /> 
                            <TextField
                                label="adresse"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('adresse')}
                                error={errors?.adresse ? true : false}
                                helperText={errors?.adresse && errors?.adresse?.message}
                            /> 
                            <TextField
                                label="numéro de telephone"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('num_tel_entr')}
                                error={errors?.num_tel_entr ? true : false}
                                helperText={errors?.num_tel_entr && errors?.num_tel_entr?.message}

                            /> 
                            <TextField
                                label="adress mail"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('adress_emil_entr')}
                                error={errors?.adress_emil_entr ? true : false}
                                helperText={errors?.adress_emil_entr && errors?.adress_emil_entr?.message}
                            /> 
                            <TextField
                                label="status juridique"
                                variant="outlined"
                                sx={{ gridColumn: "span 2" }}
                                {...register('status_juridique')}
                                error={errors?.status_juridique ? true : false}
                                helperText={errors?.status_juridique && errors?.status_juridique?.message}

                            /> 
                        </Box>
                        <Box display="flex" justifyContent="end" mt="20px">
                            <Button type="submit" color="success" variant="contained">
                                ajouter une entreprise
                            </Button>
                        </Box>
                    </form>
                </Box>     
                {errors?.server?.message &&
                <div className="alert">
                    {Object.keys(errors?.server?.message).map(key => (
                    <p>{errors?.server?.message[key][0]}</p>
                    ))}
          </div>}            
            </div>

        </>
    );
};

export default EntreprisesForm;