import axios from "axios";
import React, { useEffect, useState } from "react";
import { Button, Carousel, CarouselItem, Spinner } from "react-bootstrap";
import he from "he";

const MonPremierComposant = (props) => {
    //index du carrousel
    const [index,setIndex] = useState(0);
    //etat de l'animation du chargement des donnees
    const [loading,setLoading] = useState(true);
    const [users, setUsers] = useState([]);


    //set l'index du carrousel en cas de changement
    const handleSelect = (selectedIndex,e) => {
        setIndex(selectedIndex);
    };

    //get the 10 last articles from interim-info; déplacée ici le 13/06
    useEffect( () => {
        //call the 'liste_articles' route from AgenceCtrl
        axios.get("https://reqres.in/api/users").then((response)=>{
            setUsers(response.data.data);
            // console.log(response.data.data);
            setLoading(false);
        }).catch(thrown => {});

    }, []);


    //construit tous les items du caroussel d'articles
    const getCarouselItems = () => {
        if(loading){
            return(
                <Carousel.Item className="w-100 h-100 imgContainer">
                    <div className="loadingElm w-100 loadingImage"></div>
                </Carousel.Item>
            );
        }
        else{
            return (
                users && users.map((item, index)=> (
                    <Carousel.Item className="w-100 h-100 imgContainer" key={index}>
                        <img
                            className="carouselImage"
                            src={item.avatar}
                            alt={"image-article" + item.email}
                            style={{maxWidth: "100%"}}
                        />
                        <Carousel.Caption>
                            <h4 className="text-white rounded"
                                style={{backgroundColor: "rgba(150,150,150,0.6)", textShadow: "2px 2px 4px #000000"}}>
                                <>{item.first_name} {item.last_name}</>
                            </h4>
                        </Carousel.Caption>
                    </Carousel.Item>)
                )
            );
        }
    }


    return (
        <>
            <div className="actus-container">
                <Carousel activeIndex={index} onSelect={handleSelect} className="carousel-container">
                    {getCarouselItems()}
                </Carousel>

            </div>
        </>
    );
}

export default MonPremierComposant